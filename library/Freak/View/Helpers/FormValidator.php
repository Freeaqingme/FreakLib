<?php

class Freak_View_Helper_FormValidator extends Zend_Dojo_View_Helper_Dijit
{
    public function formValidator($formName, $url)
    {
    	$js = sprintf(
    	<<<EOL
    	dojo.addOnLoad(function(){
            var formName = "#%s";
    	    dojo.forEach(dojo.query(formName+ " input"), function(element){
    	    if(element.type != "submit" &&
                   element.type != "hidden" &&
                   element.type != "checkbox"
//                  &&    element.parentNode.id != ""
//                    && element.name != ""

            )     // We don't want elements that are already checked by dojo
                {
                    dojo.create("div",
                                {
                                    style: "display: inline;",
                                    id: element.name+"-isValid"
                                },
                                 element.parentNode
                    );

                    dojo.create("div",
                                {
                                    style: "display: block;",
                                    id: element.name+"-errors"
                                },
                                 element.parentNode
                    );
                }


                /** End 1 **/
            });


});

            function validateForm%s()
                {
                    sendForm("%s", "masterForm");
                }

              function sendForm(url, formName) {
		            var xhrArgs = {
		                form: dojo.byId(formName),
		                handleAs: "text",
		                url: url,
		                load: function(data) {
/*		                    dojo.byId("response").innerHTML = "Form posted.";
*/

		                    dojo.forEach(dojo.query("#"+formName+ " input"), function(element){
//                                dojo.html.set(dojo.query(element.name+"-errors"),"");
                                if(element.type != "submit" &&
                                    element.type != "hidden" &&
                                    element.type != "checkbox"
//                                  &&  element.parentNode.id != ""
                                    )
                                {
//	                                  if(element.value == "") {
	                                  var elementName = element.name+"-isValid";
                                      dojo.html.set(dojo.byId(elementName),
	                                        "<img src='/media/icons/Crystal/16x16/actions/button_ok.png' />");
//	                                } else {
//	                                    dojo.html.set(dojo.query(element.name+"-isValid"),"");
//	                                }
                                }
                            });

		                }
		            }
                    var defer = dojo.xhrPost(xhrArgs);
		        }
		
		        dojo.addOnLoad(validateFormmasterForm);


EOL
        ,
    	   $formName,
    	   $formName,
    	   $url
        );

        $this->view->headScript()->appendScript($js);
    	
/*        $js = sprintf('
            function validateForm'.$formName.'() {
                data = $("#'.$formName.' :input[value]").serialize();
                if (this.timer) clearTimeout(this.timer);

                this.timer = setTimeout(function () {
                $.post("%s",data,
                    function(data) {
                        $(formName+" :input").each(function() {
                            $("#"+this.name+"-errors").html("");
                            if(this.value != "") {
                                $("#"+this.name+"-isCorrect").html("<img src=\'/Images/Icons/Crystal/16x16/actions/button_ok.png\' />");
                            } else {
                                $("#"+this.name+"-isCorrect").html("");
                            }
                        });

                        errorObj = eval("(" + data + ")");
                         jQuery.each(errorObj,  function(element, errors) {
                            $("#"+element+"-isCorrect").html("<img src=\'/Images/Icons/Crystal/16x16/actions/agt_stop1.png\' />");
                            $("#"+element+"-errors").html("<ul>");
                            jQuery.each(errors, function(key, val) {
                                $("#"+element+"-errors").append("<li>"+val+"</li>");
                            });
                            $("#"+element+"-errors").append("</ul>");
                        });
                    }
                    , "json");
                },175);
            }


            $("#'.$formName.' input").load(validateForm'.$formName.');
            $("#'.$formName.' input").change(validateForm'.$formName.');
            $("#'.$formName.' input").keyup(validateForm'.$formName.');
            ',$formName,
        $url);
        $this->jquery->addOnLoad($js);*/
    }
}
