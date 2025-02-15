<!DOCTYPE html>
<html lang="en" data-critters-container data-bs-theme="green">
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="content-type">
    <meta http-equiv="Cache-Control" content="no-store">
    <meta name="viewport" content="viewport-fit=cover, width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="icon" type="image/x-icon" href="../images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Add icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <title>{$COMPETITION_NAME}</title>
  </head>

<body id="Body">
  <div class="container">

  <div class="sticky-top" style="background-image: url(Title.jpg);background-size: contain; height: 100px; background-color: rgba(255,0,0,.1);"> ></div>

  <!-- Event detail -->
  <div class="container-fluid h-100">
    <div class="row h-100 align-items-center">
      <div class="col-1 col-sm-2 col-md-3 d-none d-sm-inline">
        <img src="Logo1.jpg" class="img-fluid p-1 d-none d-sm-inline" onerror="this.onerror=null;this.src='../images/OPTIMUS3.svg'" style="max-height:100px; max-width: 100px">
        <img src="Logo2.jpg" class="img-fluid p-1 d-none d-md-inline" onerror="this.onerror=null;this.src='../images/OPTIMUS3.svg'" style="max-height:100px; max-width: 100px">
      </div>
      <div class="col-10 col-sm-8 col-md-8">
        <div class="row text-uppercase">
          <div class="col-12 h2">
            <div id="COMPETITION_NAME">{$COMPETITION_NAME}</div>
          </div>
          <div class="col-12 h4">
            <div id="DESCRIPTION">{$DESCRIPTION}</div>
          </div>
          <div class="col-12 h6">
            <div id="DATE_FROM" class="d-inline-block">{$DATE_FROM}</div> - <div id="DATE_TO" class="d-inline-block">{$DATE_TO}</div>
          </div>
          <div class="col-12 h6">
             <div id="PLACE">{$PLACE}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
<hr>

<ul class="nav nav-tabs">
  {foreach $RANK as $rank}
    <li class="nav-item"><a class="nav-link active" aria-current="page" href="?r={$rank->id}">{$rank->name}</a></li>
  {/foreach}
</ul>

  <dialog modal-mode="mega">
    <header>  <h3></h3>  <div onclick="this.closest('dialog').close()" style="max-width: 1em;cursor: pointer;">X</div>  </header>
    <div id="scoreDetails">
      <iframe src="about:blank" onload='javascript:(function(o){o.style.height=o.contentDocument.body.scrollHeight+45+"px";}(this));' style="min-height:40vh;width:80vw;min-width:300px;border:none;overflow:hidden;"></iframe>
    </div>
    <footer> <button autofocus type="reset" style="margin-left: auto; display: block;" onclick="this.closest('dialog').close('close')">Close</button> </footer>
  </dialog>
  <div id="scoreSummary" class="table-responsive p-0 w-100"> <script src="result.js"></script> </div>

  <div title="UA"> Усі результати є неофіційними до підписання головним суддею.</div> 


  <div class="my-3 bg-primary-subtle" id="footer">
    <div class="text-center">
      &copy 2024  <a href="#feedback">Команда Optimus</a>
      <a href="#privacy">Privacy Policy</a>
    </div>
  </div>

</div>  <!-- container -->

</body>
</html>