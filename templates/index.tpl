<!DOCTYPE html>
<html lang="en" data-critters-container data-bs-theme="{$THEME}">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta name="viewport" content="viewport-fit=cover, width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="icon" type="image/png" href="../../images/favicon-96x96.png" sizes="96x96">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script> var baseURL = '{$BASEURL}'; 
      //обработчик ошибок для всех изображений
      function imageError(image) {
        image.onerror = null;
        image.src = "../../images/OPTIMUS3.svg";
      }
    </script>
    <link type="text/css" rel="stylesheet" href="proto.css">
    <title>{$COMPETITION_NAME}</title>
  </head>

<body id="Body">
  <div class="container">
  {literal}
  <!-- $PAGE_LOGO_BASE64CODE should be less 64k -->
  <!--div class="sticky-top" style="background-image: url(data:image/{$PAGE_LOGO_MIME};base64, {$PAGE_LOGO_BASE64CODE}), url('Title.jpg'); background-size: contain; height: 100px; background-color: rgba(255,0,0,.1);"></div-->
  {/literal}
  <div class="sticky-top" style="background-image: url({$PAGE_LOGO}), url('Title.jpg'); background-size: contain; height: 100px;"></div>

  <!-- Event detail -->
  <div class="container-fluid h-100">
    <div class="row h-100 align-items-center text-uppercase">
      <div class="col-2 col-sm-2 col-md-1 p-0">
         <a href="/index.html" class="link-offset-2 link-offset-3-hover link-underline link-underline-opacity-0 link-underline-opacity-75-hover"><&nbsp;All events</a>
      </div>
      <div class="col-3 col-sm-3 col-md-3 d-none d-sm-inline">
        <img id="event_logo1"src="Logo1.jpg" class="img-fluid p-1 d-none d-sm-inline" onerror="imageError(this)" style="max-height:100px; max-width: 100px">
        <img id="event_logo2" src="Logo2.jpg" class="img-fluid p-1 d-none d-md-inline" onerror="imageError(this)" style="max-height:100px; max-width: 100px">
      </div>
      <div class="col-8 col-sm-8 col-md-8">
        <div class="w-100 h2">
          <div id="event_name">{$COMPETITION_NAME}</div>
        </div>
        <div class="w-100 h6">
          <div class="d-inline-block  text-nowrap" id="event_date">{$DATE_FROM}&nbsp;-&nbsp;{$DATE_TO}</div>, <div class="d-inline-block" id="event_location">{$PLACE}</div>
        </div>
      </div>
    </div>
  </div>
<hr>

<script> const eventList = {$RANK} </script>
{literal}
  <dialog modal-mode="mega">
    <header>  <h3></h3>  <div onclick="this.closest('dialog').close()" style="max-width: 1em;cursor: pointer;">X</div>  </header>
    <div id="scoreDetails">
      <iframe src="about:blank" onload='javascript:(function(o){o.style.height=o.contentDocument.body.scrollHeight+45+"px";}(this));' style="min-height:40vh;width:80vw;min-width:300px;border:none;overflow:hidden;"></iframe>
    </div>
    <footer> <button autofocus type="reset" style="margin-left: auto; display: block; width: 100px" onclick="this.closest('dialog').close('close')">Close</button> </footer>
  </dialog>
  <div id="scoreSummary" class="table-responsive p-0 w-100"> <script src="result.js"></script> </div>
{/literal}
  <!--div title="UA"> Усі результати є неофіційними до підписання головним суддею.</div--> 
  <div title="EN"><small>All results are unofficial until signed by the chief judge.</small></div> 


  <div class="my-3 bg-primary-subtle" id="footer">
    <div class="text-center">
      &copy 2025  <a href="../../contact.html">Команда Optimus</a>
      <a href="#privacy">Privacy Policy</a>
    </div>
  </div>

</div>  <!-- container -->

</body>
</html>