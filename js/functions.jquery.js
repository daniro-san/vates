(function($) {
  // Serialize Form to Nokade Format
  $.fn.serializeJSON=function() {
		var json = {};
		var self = this;
		var disabled = $( self ).find( ":disabled" ).removeAttr( "disabled" );
		jQuery.map($(this).serializeArray(), function(n, i){
			// Atribuindo valores, ja tratando caracteres especiais
			var name = n['name'];
			
			// Verifica a família (INPUT, TEXTAREA, SELECT, etc.) do elemento em questão...
			var elmnt = $( self ).find("[name='"+name+"']").get(0).nodeName;  
			// console.log(  "Element #"+i+": N=" + name + ", E=" + elmnt + ", T=" + ( elmnt=="INPUT" ? $("[name='"+name+"']").attr("type") : "..." ) );
			
			if( name!="_wysihtml5_mode" ) {
				
				var field = name;
				var value = "";
				
				// Validar tipos dos elementos...
				switch (elmnt) {
					
					case "TEXTAREA":
							field = field.replace( "_TEXTAREA", "" );
							value = ( String( $( self ).find( "[name="+n["name"]+"]" ).val( ) ).split('"' ).join( '\"' ) ).replace(/\n/g, ' &#13;'); 
						break;
						
					case "INPUT":
						var type = $(self).find('[name='+name+']').attr("type");
						switch (type){
							case "checkbox":
								field = field.replace( "_CHECKBOX", "" );
								value = $( self ).find( "[name="+n["name"]+"]:checked" ).map(function () {
									return this.value;
								}).get().toString();
								break;
							case "radio":
								field = field.replace( "_RADIO", "" );
								value = String( $( self ).find( "[name="+n["name"]+"]:checked" ).val () ).split( '"' ).join( '\"' ); 
								break;
							default:
								value = String( $( self ).find( "[name="+n["name"]+"]" ).val( ) ).split( '"' ).join( '\"' ); 
						}
						break;
						
					default:
						value = String( $( self ).find( "[name="+n["name"]+"]" ).val( ) ).split( '"' ).join( '\"' ); 	
				}
				
				json[field] = value;	
				
				/*
				var name_test = name.split("_");
				var size = name_test.length;
				
				if( name_test[size-1] == "TEXTAREA" ) {
					name = "";
					for( var i = 0; i <= size-2; i++ ) {
						if(i != 0)
							name += "_";
						name += name_test[i];
					}
					
					var value = String( $( "[name="+n["name"]+"]" ).val( ) ).split('"' ).join( '\"' ); 
					json[name] = value.replace(/\n/g, ' &#13;');	
				} else if ( name_test[size-1] == "CHECKBOX" ) {
					name = "";
					for( var i = 0; i <= size-2; i++ ) {
						if(i != 0)
							name += "_";
						name += name_test[i];
					}
					
					var value = String( $( "[name="+n["name"]+"]:checked" ).val () ).split( '"' ).join( '\"' ); 
					json[name] = value;	
				} else if ( name_test[size-1] == "RADIO" ) {
					
					var field = String( name ).replace("_RADIO","");
					var value = String( $( "input[name="+n["name"]+"]:checked" ).val () ).split( '"' ).join( '\"' ); 
					
					json[field] = value;	
					
				} else {
					var value = String( $( "[name="+n["name"]+"]" ).val( ) ).split( '"' ).join( '\"' ); 
					json[name] = value;	
				}*/
				
			}
			
		});
		jsonString = JSON.stringify(json);
		disabled.attr( "disabled", "disabled" );
	    return jsonString;

	 };
	
	//Verifica se esta vazio
	$.fn.validateForm = function(){
		
	    var validate = true;
				
		// Campo vazio
		$(this).find(".required").each(function(){
			var element  = $(this).get(0).nodeName;
			var self = this;
							
			switch (element) {
				case "TEXTAREA":
						if($(self).val() === "") {
							$(self).css("background-color","#fde3e6");	
							
							validate = false;
						} else {
							if($(self).attr("readonly"))
								$(self).css("background-color","#eeeeee");
							else
								$(self).css("background-color","#fff");
						}
					break;
					
				case "SELECT":
						if(!$(self).val() || $(self).val() === 0) {
							
							if($(self).hasClass("chosen-select")) {
								$(self).next("div").find("a").css("background-color","#fde3e6");
							} else {
								$(self).css("background-color","#fde3e6");
							}
							
							validate = false;
						} else {
							if($(self).hasClass("chosen-select")) {
								$(self).next("div").find("a").css("background-color","#fff");
							} else {
								if($(self).attr("readonly"))
									$(self).css("background-color","#eeeeee");
								else
									$(self).css("background-color","#fff");
							}
						}
					break;
					
				case "INPUT":
					var type = $(self).attr("type");
					// alert(self);
					switch (type){
						case "checkbox":
							if($("input[name='"+self.name+"']:checked").length < 1) {
								
								if($(self).next("i").length > 0) {
									$(self).next("i").css("background-color","#fde3e6");
								} else {
									$(self).css("background-color","#fde3e6");
								}
								
								validate = false;		
							} else {
								if($(self).next("i").length > 0) {
									$(self).next("i").css("background-color","#fff");
								} else {
									$(self).css("background-color","#fff");
								}
							}
							break;
						case "radio":
							if($("input[name='"+self.name+"']:checked").length < 1) {
								
								if($(self).next("i").length > 0) {
									$(self).next("i").css("background-color","#fde3e6");
								} else {
									$(self).css("background-color","#fde3e6");
								}
								
								validate = false;		
							} else {
								if($(self).next("i").length > 0) {
									$(self).next("i").css("background-color","#fff");
								} else {
									$(self).css("background-color","#fff");
								}
							}
							break;
						default:
							if($(self).val() === "") {
								$(self).css("background-color","#fde3e6");	
								
								validate = false;
							} else {
								if($(self).attr("readonly"))
									$(self).css("background-color","#eeeeee");
								else
									$(self).css("background-color","#fff");
							}
					}
					
				break;	
			}
	    });
		
		return validate;
		
		/* Validando email
		var emailfilter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
	    $(this).find("input[name=DS_EMAIL]").each(function(){
	        if(!emailfilter.test($(this).val())){
	            $(this).css("background-color","#f4fab1");
	            validate = false;
	        }else{
				$(this).css("background-color","#ffffff");
			}
	    });
		*/
	};
	
	//Limpa o formulário
	$.fn.clearForm = function(callback){
		
		$(this.selector)[0].reset();
			
		// Campo vazio
		$(this).find(".form-control").each(function(){
			var element  = $(this).get(0).nodeName;
			var self = this;
							
			switch (element) {
				case "TEXTAREA":
						$(self).css("background-color","#fff");
						$(self).val("");
					break;
					
				case "SELECT":
						if($(self).hasClass("chosen-select")) {
							$(self).val( "" ).trigger( "chosen:updated" );
							$(self).next("div").find("a").css("background-color","#fff");
						} else {
							$(self).css("background-color","#fff");
							$(self).val(0);
						}
					break;
					
				case "INPUT":
					var type = $(self).attr("type");
					// alert(self);
					switch (type){
						case "checkbox":
							if($(self).next("i").length > 0) {
								$(self).next("i").css("background-color","#fff");
							} else {
								$(self).css("background-color","#fff");
							}
							
							$(self).prop("checked", false);
							
							$(self).trigger("change");
							break;
						case "radio":
							if($(self).next("i").length > 0) {
								$(self).next("i").css("background-color","#fff");
							} else {
								$(self).css("background-color","#fff");
							}
							
							$(self).prop("checked", false);
							
							$(self).trigger("change");
							break;
						default:
                            if($(self).is('[readonly]') === false){
							    $(self).css("background-color","#fff");
                            }
                            
							$(self).val("");
					}
					
				break;
					
			}
	    });
		
		if ( callback && typeof( callback ) === "function" ) {
			callback( );
		}
		
		return this;
	};
})(jQuery);