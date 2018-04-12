// var config = {
//     'link' : {
//         'start' : 'link'
//     }
// }; 


/** ******************** Function permettant de supprimer les balises html ******************* **/
/** ****************************************************************************************** **/
    function strip_tags(input, allowed)
    {
        allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
        var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi; commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) { return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''; });
    }
/** ****************************************************************************************** **/
/** ******************* Function permettant de comparer le html avec le doc ****************** **/
/** ****************************************************************************************** **/
    function DocHtmCompare(htm,doc)
    {
        
        var data   = $.trim(doc);
            data   = encodeURIComponent(data);          

        var find = '%C2%A0'; var re = new RegExp(find, 'g'); data = data.replace(re, ' ');
        var find = '%E2%80%99'; var re = new RegExp(find, 'g'); data = data.replace(re, '%27');

            data = decodeURIComponent(data);
        
        var find = '&nbsp;'; var re = new RegExp(find, 'g'); data = data.replace(re, ' ');
        var tabDoc = data.split("^^");
       
        tabDoc = jQuery.grep(tabDoc, function(value) { if(value!="" && value!="\n") return value; });
   
        //var find = '…'; var re = new RegExp(find, 'g'); htm = htm.replace(re, '...');

        $("body").append('<div id="tmphtml"></div>'); 
        $("#tmphtml").append(htm); 
        $("#tmphtml img").remove();
       
        // recuperer le div qui contient le text html "main"
      
        // var obj  = $("#tmphtml div:eq(1)");
        var obj  = $("#tmphtml");
        $("style, select, input, script, image").remove();
        var j = 0;
        var tabHtm  = [];
        var tabHtm_ = [];
       /**  début Modification
        *   Ajouter de support de plusieurs balises ainsi qu'un filtrage des redondances
        */
       var targets = "h3, h2, h1, h4, h5, p, li, tr, i, b, td";  // les balises à chercher
       console.log(obj);
        $(targets,obj).each(function()
        {
            var temp =  $.trim($(this).html());
            console.log(temp);
            var _targets_ = targets;
            var tag = $(this).prop('tagName').toLowerCase();

            _targets_ = _targets_.split(',').map(function(b){
                return b.trim().toLowerCase(); 
            });
            _targets_.splice(_targets_.indexOf(tag),1); 

            //console.log(tag);
                
            sniffTable = $(this).parents().map(function() {
                if(_targets_.indexOf(this.tagName.toLowerCase()) != -1 ){
                    return 'tained';
                } else {
                    return 'clean';
                }
            }).get(); 
            //console.log(sniffTable); 
            if(sniffTable.indexOf("tained") == -1){
                tabHtm[j] = temp; 
                //console.info(temp);
            } else {
                //console.error(temp);
            } 
            
            j++;
        });

        tabHtm = tabHtm.map(function(value, key){
            if(value){
                return strip_tags(value , "b, u, link, ub, i"); 
            }
        }); 
        // console.info(tabHtm); 
        $('#tmphtml').remove();
        var timer  = 0; 
        var timerHandler = setInterval(function(){
            timer ++;
            $('#timeInterval').html(timer+ " secondes"); 
        },1000);  
        var request = $.post(
            base_url+"index.php?c=ajax/process", 
            {
                "html"        : tabHtm, 
                "copyright"   : tabDoc
            }
        ); 
        
        $('#diff').html('Chargement ...');    
        request.done(function(data){
            $('#diff').html(data); 
            clearInterval(timerHandler);
            $('#timeInterval').html("Généré en "+ timer +" secondes");

            var html_errors = $('#diagnostics-errors .html').text(); 
            var copyright_errors = $('#diagnostics-errors .copyright').text(); 

            // $('#codered .error-count').text(copyright_errors); 
            // $('#codegreen .error-count').text(html_errors); 

        });     

        $("#blockHtm").hide();
        $("#blockDoc").hide();

        /**/
        // var content = $("#diff").html();
        // content = $("#diff").html();
        /* replace links */

        // $("#diff").html(content); 

        $("#content").fadeIn(1000);
    }
/** ****************************************************************************************** **/
$(document).ready(function()
{
    /** ~~~~~~~~~~~~~ Button uplaod ~~~~~~~~~~~~~ **/
    /** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ **/
    
        $("input[type='file']").on("change",function(){ fileCount = this.files.length;if(fileCount>1) { $("span.nofile",$(this).parent()).fadeOut("fast"); $("span.filename",$(this).parent()).html(fileCount+" fichiers sélectionner").fadeIn("slow"); } else { var val = $(this).val().substr($(this).val().lastIndexOf('\\')+1); if(val!="") { $("span.nofile",$(this).parent()).fadeOut("fast"); $("span.filename",$(this).parent()).html(val).fadeIn("slow");} else { $("span.filename",$(this).parent()).html(val).fadeOut("fast"); $("span.nofile",$(this).parent()).fadeIn("slow"); } } });
   
    /** ~~~~~ Button change html type input ~~~~~ **/
    /** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ **/
    
        $("a#htmlSwitch").on("click",function() { if($("div#cntHtmlFile").css("display")=="none"){ $("div#cntHtmlFile").fadeIn("fast",function(){ $(this).addClass("active"); $("input",this).val(""); }); $("div#cntHtmllink").fadeOut("fast",function(){ $(this).addClass("active"); $("input",this).val(""); }); } else { $("div#cntHtmlFile").fadeOut("fast",function() { $("span.filename",this).html("").fadeOut("fast"); $("span.nofile",this).fadeIn("slow"); $(this).removeClass("active"); $("input",this).val(""); }); $("div#cntHtmllink").fadeIn("fast",function(){ $(this).removeClass("active"); $("input",this).val(""); }); } return false; });
    

    /** ~~~~~ Button add an other doc input ~~~~~ **/
    /** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ **/
    
        $("a#addDoc").on("click",function() { 
            if($("div#cntDocFilePlus").css("display")=="none") {
                $("a#addDoc img").attr("src",base_url+"resource/imgs/moin.png"); 
                $("div#compForm").animate({"height":"105px"},100,function() { 
                    $("#cntDocFilePlus").fadeIn("fast"); 
                    $("#compForm div#cntBtnSubmit input[type='submit']").css('margin-top','80px'); 
                }); 
            } else { 
                $("a#addDoc img").attr("src",base_url+"resource/imgs/plus.png"); 
                $("#cntDocFilePlus").fadeOut("fast",function() { 
                    $("span.filename",this).html("").fadeOut("fast"); 
                    $("span.nofile",this).fadeIn("fast"); 
                    $("div#compForm").animate({"height":"70px"},100); 
                    $("#compForm div#cntBtnSubmit input[type='submit']").css('margin-top','44px'); 
                }); 
            } 
            return false; 
        });
   
    /** ~~~~~~~ Button submit compare form ~~~~~~ **/
    /** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ **/

       $("#compForm form input[type='submit']").on("click",function()
       {
            var obj = $(this).parent().parent();
            /*obj.append('<div id="loader"/>');
            $("#loader",obj).fadeIn("slow");*/
            $('#loader').show(); 
            obj.attr({"target":"form_submit","method":"POST","action":base_url+"index.php?c=ajax/load","enctype":"multipart/form-data"});
            if(!$("#form_submit").is("iframe")) $("body").append('<iframe id="form_submit" name="form_submit"></iframe>');
            obj.submit();
            $("iframe#form_submit").load(function()
            {
                // get Html Content
                resHtm = $("iframe#form_submit").contents().find('body #loadHtm').html();

                result =  $.parseHTML(resHtm);

                $('body').append('<div id="emptyTestBlock" style="display:none"></div>'); 

                $('#emptyTestBlock').html(result); 

                var matches = function(val){ return false; 
                   /* var pattern  = {
                        'link' : new RegExp("<link>.*</link>", 'g'),
                        'bold' : new RegExp("<b>.*</b>", 'g'),
                        'ub' : new RegExp("<ub>.*</ub>", 'g'),
                        'underlined' : new RegExp("<u>.*</u>", 'g')
                    };
                    if( ( val.indexOf('<link>') != -1 && val.indexOf('</link>') != -1) ||
                        ( val.indexOf('<b>') != -1 && val.indexOf('</b>') != -1) ||
                        ( val.indexOf('<u>') != -1 && val.indexOf('</u>') != -1) ||
                        ( val.indexOf('<ub>') != -1 && val.indexOf('<ub>') != -1)
                    ) */return true;  
                }; 
                var validate  = function($this, target){
                    var result  = $this.parents().map(function() {
                        if(target.indexOf(this.tagName.toLowerCase()) != -1 ){
                            return 'tained';
                        } else {
                            return 'clean';
                        }
                    }).get();
                    if(result.indexOf('tained') == -1){
                        return true; 
                    } else {
                        return false; 
                    }
                };
                $('#emptyTestBlock').find('a b, b a,a strong, strong a').map(function(k, val){
                    var text = $(val).text();  ; 
                    var cmp = $(val).html();
                    if(!matches(text) && validate($(this) , ['link']) ){
                        return $(val).text('<link>'+text+"</link>");
                    }
                }); 

                
                $('#emptyTestBlock').find('b u, strong u, u b, u strong').map(function(k, val){
                    var text = $(val).text();
                    if(!matches(text) && validate($(this), ['ub', 'a', 'link'])) {
                        return $(val).text('<ub>'+text+"</ub>");
                    }
                }); 

                $('#emptyTestBlock').find('b, strong').map(function(k, val){
                    var text = $(val).text();
                    if(!matches(text) && validate($(this), ['a', 'b', 'strong', 'link'])){
                        return $(val).text('<b>'+text+"</b>");
                    }
                });
                
                $('#emptyTestBlock').find('u').map(function(k, val){
                    var text = $(val).text();
                    if(!matches(text) && validate($(this), ['a', 'u', 'link'])){
                        return $(val).text('<u>'+text+"</u>");
                    }
                });


                resHtm = $("#emptyTestBlock").html(); 

                $("#emptyTestBlock").remove(); 


                // get Doc Content
                resDoc = $("iframe#form_submit").contents().find('body #loadDoc').html();
                
                /**
                * clear document content
                */
                resDoc = resDoc.replace(/<link>[\s\r\n\t]*<\/link>/g, ''); 
                resDoc = resDoc.replace(/<b>[\s\r\n\t]*<\/b>/g, ''); 
                resDoc = resDoc.replace(/<ub>[\s\r\n\t]*<\/ub>/g, ''); 
                resDoc = resDoc.replace(/<u>[\s\r\n\t]*<\/u>/g, '');

                $("#compForm").animate({"top":"500px","margin-top":"0px",},0,function()
                {
                    // $("#compForm").fadeOut("fast",function() {  });
                    $(this).hide();
                   
                    // Start Compare Documents
                    DocHtmCompare(resHtm,resDoc);
                    $("iframe#form_submit").hide();
                    return false;
                });
                return false;
            });
            return false;
       });
    /** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ **/
    /** ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ **/
});