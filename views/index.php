<html>
    <head>
    	<meta charset="utf-8"/>
    	<title>Outils | Comparateur</title>
        <link rel="stylesheet" href="<?php echo RESOURCE; ?>css/styles.css"/>
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
        <script type="text/javascript" src="<?php echo RESOURCE; ?>js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo RESOURCE; ?>js/fct.js"></script>
        <script type="text/javascript"> var base_url = "<?php echo BASE_URL; ?>"; </script>
    </head>

    <body>
        <div class="title">
            Comparateur Des Pages html Vs Copyright (.docx file)
        </div>

        <div id="content">

            <div id="indice_color">

                <div id="codeword"><div id="codered"><span class="error-count"></span></div>Texte <b>Copyright</b>, n'existe pas dans L'HTML </div>

                <div id="codehtml"><div id="codegreen"><span class="error-count"></span></div>Texte <b>HTML</b>, n'existe pas dans Copyright </div>
                
                <div id="timeInterval"></div>

            </div>

            <div id="blockDoc" class="cntBlock original invisible"><div id="icnDoc"></div></div>

            <div id="blockHtm" class="cntBlock changed invisible"><div id="icnHtm"></div></div>

            <div id="diff" class="cntBlock diff"><div id="icnDiff"></div></div>

        </div>

        <div id="compForm">

            <div id="loader"><i class="fa fa-refresh fa-spin"></i></div>

            <form>

                <a href="#" id="htmlSwitch"><img src="<?php echo RESOURCE ; ?>imgs/switch.png"/></a>

                <div class="uploader" id="cntHtmlFile">

                    <span class="nofile">Sélectionner le fichier html</span>

                    <span class="filename"></span>

                    <input type="file" id="htmlFile" name="htmlFile" size="18.5" style="opacity:0;"/>

                    <span class="action">Sélectionner un fichier html</span>

                </div>

                <div id="cntHtmlLink">

                    <input type="text" id="htmlLink" name="htmlLink" placeholder="Lien du fichier html"/>

                </div>

                <div class="uploader" id="cntDocFile">

                    <span class="nofile">Sélectionner un document word</span>

                    <span class="filename"></span>

                    <input type="file" id="docFile" name="docFile" size="18.5" style="opacity:0;"/>

                    <span class="action">Sélectionner un document word</span>

                </div>

                <div class="uploader" id="cntDocFilePlus">

                    <span class="nofile">Sélectionner un autre document word</span>

                    <span class="filename"></span>

                    <input type="file" id="docFilePlus" name="docFilePlus" size="18.5" style="opacity:0;"/>

                    <span class="action">Sélectionner un autre document word</span>

                </div>

                <a href="#" id="addDoc"><img src="<?php echo RESOURCE ;?>imgs/plus.png"/></a>

                <div id="cntBtnSubmit">

                    <input type="submit" id="formSub" name="formSub" value="Comparer" class="bBlack"/>

                </div>

            </form>

        </div>

    </body>

</html>