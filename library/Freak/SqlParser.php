<?php

class Freak_SqlParser extends Sql {

    public function getEasyWhereTree($query) {
        $this->parse($query);
        if(($error = $this->getError()) !== null && $error instanceof exception) {
            throw $error;
        }
        return $this->_parseQueryElement($this->getWhere());
    }

   /** Derived from Sql_Compiler::compileSearchClause()
    *  and Sql_Compiler::getWhereValue();
    */
    protected function _parseQueryElement($element, $depth = 0) {
        $depth++;
        if($depth > 75) {
            // Reproduce by query (see missing = sign at last):
            // $query = "SELECT * FROM `users` WHERE id <> 0 OR id =1 OR id 2";
            throw new Freak_SqlParser_Exception_QueryUnparsable(
                'Recursion level of 50 reached'
            );
        }

        $out = array();
        if (isset ($element['Left']['Value'])) {
            $key = $this->_getQueryElementPartValue($element['Left'], $depth);
            if(is_array($key)) {
                $out[] = $key;
            }
        } else {
            if($element != null) {
    			$out = $this->_parseQueryElement($element['Left'], $depth);
            }
		}

		if(isset($element['Op'])) {
            if(strtoupper($element['Op']) == 'BETWEEN') {
                $out[] = array($key,
                               $element['Op'],
                               $element['Right']['Value']['Left']['Value'],
                               $element['Right']['Value']['Right']['Value']);
            } elseif(strtoupper($element['Op']) == 'IS') {
                if (isset ($where_clause['Neg'])) {
                    $out[] = array($key, 'IS', 'NOT NULL');
				} else {
				    $out[] = array($key, 'IS', 'NULL');
				}
            } elseif(strtoupper($element['Op']) == 'IN') {
                if($element['Right']['Type'] == 'command') {
                    //@todo when/if supporting subqueries, also add checks to
                    // Core_Model_Mapper_Library_FilterUserParser
                    throw new Freak_SqlParser_Exception_QueryUnparsable(
	    				'No subqueries supported'
					);
					
                }

                $values = $this->_getMultiValueValues($element['Right']);
                $out[] = array($key, $element['Op'], $values);
            } else {
                if(isset($element['Right']['Value'])) {
                    try {
                    $value = $this->_getQueryElementPartValue($element['Right'], $depth);
                    } catch(exception $e) {
                        var_dump($element); exit;
                    }
                    $out = array($key, $element['Op'], $value);
                } else {
                    $out[] = $element['Op'];
                    $out[] = $this->_parseQueryElement($element['Right'], $depth);
                }
            }
		}

		if(isset($element['Function'])) {
            $out['Function'] = $element['Function'];
        }

		return $out;
    }

    /**
     * Derived from Sql_Compiler::getParams();
     * Enter description here ...
     * @param unknown_type $node
     */
    protected function _getMultiValueValues($node) {
		for ($i = 0; $i < count ($node['Type']); $i++) {
			switch ($node['Type'][$i]) {
				case 'ident':
				case 'real_val':
				case 'int_val':
					$values[] = $node['Value'][$i];
					break;
				case 'text_val':
					$values[] = self::ESCAPE .$node['Value'][$i]. self::ESCAPE;
					break;
				default:
					throw new Freak_SqlParser_Exception_QueryUnparsable(
						'Unknown type: '.$node['Type']
					);
			}
		}
		return $values;
    }

    protected function _getQueryElementPartValue($element, $depth) {
        switch($element['Type']) {
			case 'subclause':
                return $this->_parseQueryElement($element['Value'], $depth);
                break;
			case 'ident':
			case 'int_val':
			case 'text_val':
			    return $element['Value']; // Dont escape here
			default:
                throw new Freak_SqlParser_Exception_QueryUnparsable(
	    			'Sql_Compiler::getWhereValue(), type = '.$element['Type']
                );
        }
    }
}
