<!-- $Id$ -->
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>Ebayseller</title>
	{STYLESHEETS}
   {CRMCSS}
   {JQUERY}
<script type="text/javascript" src="lxcars/inc/lxcjquery.js"></script>

<script language="JavaScript">   
 $(document).ready(function()
   {
   //Submit-Event an das Formular binden
   $('form').submit(function()
   {
      //IFrame erzeugen
      $('body').append('<iframe name="uploadiframe" src="/upload.php" ></iframe>');
      
      //Ziel des Formulars auf das IFrame verweisen
      $(this).attr('target','uploadiframe');
      
      //Load-Event an das IFrame binden
      $('#uploadiframe').load(function()
      {
         //Datei wurde hochgeladen
      })
   });
})

</script>

   {JQUERYUI}
   {THEME}

	</head>
	
<body >
   {PRE_CONTENT}
   {START_CONTENT}
   {TEST}
   <div>
    <form enctype="multipart/form-data" method="post" action="/upload.php">
            <input type="file" name="file" />
            <button type="submit">Datei hochladen</button>
    </form>   
   </div>
   {END_CONTENT}
</body>
</html>

