<!doctype html>
<html class="no-js" lang="">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>model-php2ts</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="node_modules/normalize.css/normalize.css" type="text/css" rel="stylesheet"/>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
  <link href="node_modules/dropzone/dist/min/dropzone.min.css" type="text/css" rel="stylesheet"/>
  <link href="assets/css/app.css" type="text/css" rel="stylesheet"/>
  <script src="node_modules/dropzone/dist/min/dropzone.min.js"></script>
</head>
<body>


<main class="container-fluid">
  <div class="row">
    <div class="col-xs-6 php-box">
      <header class="php-header">
        <img src="assets/img/php.png">
      </header>
      <form action="app/upload.php/"
            method="post"
            enctype="multipart/form-data"
            class="dropzone"
            id="dropzone">
      </form>
    </div>

    <div class="col-xs-6">
      <header class="ts-header">
        <img src="assets/img/ts.png">
      </header>
      <div class="ts-box">
        <span id="placeholder-text">Wait for it &hellip;</span>

        <div class="lds-css ng-scope" id="conversion-loader">
          <div style="width:100%;height:100%" class="lds-rolling">
            <div></div>
          </div>
        </div>

        <a href="ts-models.zip" id="ts-models-link">
          <img src="assets/img/download.png">ts-models.zip
        </a>
      </div>
    </div>
  </div>
</main>

<footer>
  <div>
    PHP Models
  </div>
  <div>
    Typescript Models
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="assets/js/app.js"></script>

</body>
</html>
