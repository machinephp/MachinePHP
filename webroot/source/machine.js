function machine_js()
{
        
        var result;
        var html = '';
        
        var compReady = true;

        this.x = new Object();
        this.util = new machineUtility_js();
        this.form = new machineForm_js();

        //AJAX Functionality methods   -START-

        this.load  = function(path,area,params)
        {
            if (typeof params == 'undefined') params = new Object();
           if(!area)
                {
                   location.href=path; 
                }    //area = "main";
                else
                    {
                   var content = $.ajax({
                          url: path,
                          type: "POST",
                          data: params,
                          dataType: "html",
                          success: function(content){document.getElementById('mar_' + area).innerHTML = content;if(area=="main")location.href='#'+path;}
                       }
                    ).responseText;
                    }
                 //document.getElementById('machine_' + area).innerHTML = content;
            
        }
        
        this.reload = function()
        {
            window.location.reload();
            //location.reload(true);
        }

        this.data = function(path,params)
        {
            if (typeof params == 'undefined') params = new Object();
            $.ajax({
                      url: '/_-_data_-_' +path,
                      type: "POST",
                      data: params,
                      dataType: "json",
                      async: false,
                      success: function(result){
                       machine.result=result;
                      }
                   }
                );
                
            var result = machine.result;
            machine.result = null;
            if(result==null)
                return false;
            return result.data;
        }

        this.proc = function(path,params)
        {
            if (typeof params == 'undefined') params = new Object();
            $.ajax({
                      url: '/_-_proc_-_' +path,
                      type: "POST",
                      data: params,
                      dataType: "json",
                      async: false,
                      success: function(result){
                       machine.result=result;
                      }
                   }
                );

            var result = machine.result;
            machine.result = null;
            if(result==null||result==false)
                return false;
            return result.data;
        }
        
        this.obj = function(obj,method,params)
        {
            if (typeof params == 'undefined') params = new Object();
            var parameters = new Object();
            
            parameters._m_obj_params = params;
            $.ajax({
                      url: '/_-_obj_-_/' + obj + '/' + method,
                      type: "POST",
                      data: parameters,
                      dataType: "json",
                      async: false,
                      success: function(result){ _m.result=result; },
                      error: function(a,b,c){alert(print_r(c,true)); }
                   }
                );
            var result = _m.result;
            
            _m.showErrors(result.error);
            _m.showMessages(result.message);
            _m.result = null;
            if(result==null||result==false)
                return false;
            return result.data;
        }
        
        
        this.comp = function(name,compName,params)
        {
            if (typeof params == 'undefined') params = new Object();
            params._m_name = name;
            params._m_comp_name = compName;
            
            $.ajax({
                      url: '/_-_comp_-_' + location.pathname, // + '--' + compName
                      type: "POST",
                      data: params,
                      dataType: "html",
                      async: false,
                      success: function(result){
                       _m.html=result;
                      }
                   }
                );

            var html = _m.html;
            _m.html = '';
            
            
            
            if(html==null||html==false)
                return false;
            
            //alert($('#_m_' + name).html());
            
            
            if($('#_m_' + name).exists())
            {
                //$('#_m_' + name).fadeOut('fast');
                $('#_m_' + name).html(html);
                //$('#_m_' + name).fadeIn('fast');
            }
            
            return html;
        }
        
        this.error = function(text)
        {
            //@TODO: make decent modal window
            $.prompt(text);
        }

        this.message = function(text)
        {
            //@TODO: make decent modal window
            $.prompt(text);
        }

        this.showErrors = function(errors)
        {
            if(!errors.length) return true;

            var errorText = '';
            var errorHTML = '';

            for(i in errors)
            {
                errorText += errors[i] + "\n";
                errorHTML += errors[i] + "\n";
            }

            if($('#errors').exists())
                $('#errors').html(errorHTML);
            else
               _m.error(errorText);   
        }

        this.showMessages = function(messages)
        {
            if(!messages.length) return true;

            var messageText = '';
            var messageHTML = '';

            for(i in messages)
            {
                messageText += messages[i] + "\n";
                messageHTML += messages[i] + "\n";
            }

            if($('#messages').exists())
                $('#messages').html(messageHTML);
            else
                _m.message(messageText);   
        }
        
        

        //AJAX Functionality methods   -END-


        //Display methods   -START-
        this.alert = function(text)
        {
            $.prompt(text);
        }

        //Display methods   -END-
       
}

function machineUtility_js()
{
    //UTILITY FUNCTIONS

        this.showHide = function(divID,element)
        {
            var curDiv = document.getElementById(divID);
            if(!curDiv)
                return;

            var curState = curDiv.style.visibility;

            if(curState=='collapse')
            {
                document.getElementById(divID).style.visibility = 'visible';
                element.innerHTML = "hide";
            }
            else
            {
                document.getElementById(divID).style.visibility = 'collapse';
                element.innerHTML = "show";
            }


        }

        this.trim = function(stringToTrim) {
                        if(stringToTrim)
                            return stringToTrim.replace(/^\s+|\s+$/g,"");
                        else
                            return '';
		}
        this.ltrim = function(stringToTrim) {
                return stringToTrim.replace(/^\s+/,"");
        }
        this.rtrim = function(stringToTrim) {
                return stringToTrim.replace(/\s+$/,"");
        }

        this.formatDate = function(dateString)
        {
            var fDate = '';
            var dateParts = dateString.split('-');

            var year = dateParts[0];
            var month = dateParts[1];
            var day = dateParts[2];

            if(parseInt(month)<10)
                month = '0' + parseInt(month);

            switch(month)
            {
                case '01':
                  month = 'Jan';
                  break;
                case '02':
                  month = 'Feb';
                  break;
                case '03':
                  month = 'Mar';
                  break;
                case '04':
                  month = 'Apr';
                  break;
                case '05':
                  month = 'May';
                  break;
                case '06':
                  month = 'Jun';
                  break;
                case '07':
                  month = 'Jul';
                  break;
                case '08':
                  month = 'Aug';
                  break;
                case '09':
                  month = 'Sep';
                  break;
                case '10':
                  month = 'Oct';
                  break;
                 case '11':
                  month = 'Nov';
                  break;
                 case '12':
                  month = 'Dec';
                  break;
            }

            fDate = month + ' ' + day + ', ' + year;

            return fDate;
        }

        this.formatTime = function(timeString)
        {
            var fTime = '';
            var timeParts = timeString.split(':');

            var hour = timeParts[0];
            var min = timeParts[1];
            var sec = false;

            if(timeParts[2]!=undefined)
                sec = timeParts[2];

            var ampm = 'AM';
            if(hour>12)
                {
                    hour = hour - 12;
                    ampm = 'PM';
                }

            if(min<10)
                {
                    min = '0' + min;
                }

            fTime = hour + ':' + min + ' ' + ampm;


            return fTime;
        }

        this.loadStyles = function()
        {
             $(".listElement").mouseover( function()
                {
                this.style.backgroundColor = 'lightyellow';
                }
                )

             $(".listElement").mouseout( function()
                {
                this.style.backgroundColor = 'transparent';
                }
                )

        }

        this.imageChange = function(elementID)
        {
            var cur = document.getElementById(elementID).value;
            document.getElementById(elementID).value = cur.replace('http://a.b.c','');
        }

        this.urlizeString = function(str)
        {
            //@TODO: write
            return str;

        }

        this.validateInput = function(input)
        {
            //@TODO: write input validation

            return true;
        }
}

function machineForm_js()
{
    
}

function machineAuth_js()
{
    
}



var _m = new machine_js();

jQuery.fn.exists = function(){return jQuery(this).length>0;}

$(document).ready(
function () 
{
    if(typeof _m.app.onLoad=='function')
        _m.app.onLoad();
}
)

function machineXTag_js()
{
    this.getHTML = function(content_id,tag_id,title)
    {
        var html = '';
        
        html = '<div class="w3cbutton4" onClick="_m.x.tag.remove(' + content_id + ', ' + tag_id + ', this)" id="tag_' + content_id + '_' + tag_id + '"><a href="#"><span class="spec">' + title + '</span></a></div>';
        
        return html;
    }
    
    this.place = function(content_id,data,field_id)
    {
        $('#' + field_id).append(_m.x.tag.getHTML(content_id,data.tag_id,data.title));
        
        this.setButtonBehavior();
    }
    
    this.setButtonBehavior = function()
    {
        $('.tag_button').css('color','red');
    }

    this.add = function(content_id,tag,field_id)
    {
        //@TODO: change tag name to tag id
       
        var data = _m.obj('topic','find',new Array(tag));

        this.place(content_id,data[0],field_id);

        $('#tag_select').val('');
    }

    this.addText = function(content_id,text,field_id)
    {
        //@TODO: create tag

        var data = _m.obj('tag','create',new Array(text));
        this.place(content_id,data,field_id);
        $('#tag_select').val('');
        return false;
        
    }

    this.remove = function(content_id,tag,field)
    {
        $('#' + field.id).remove();
    }
}

_m.x.tag = new machineXTag_js();