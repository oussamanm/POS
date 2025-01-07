<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <link rel=icon href=/images/favicon.ico>
    <link rel="stylesheet" href="/css/master.css">

    <title>Stockly | Gestion de Stock et Vendeurs</title>
  </head>

  <body class="text-left">
    <noscript>
      <strong>
        Nous sommes désolés mais Stockly ne fonctionne pas correctement sans l'activation de JavaScript. S'il vous plait, activez le pour continuer.</strong
      >
    </noscript>

    <!-- built files will be auto injected -->
    <div class="loading_wrap" id="loading_wrap">
      <div class="loader_logo">
      <img src="/images/avatar/logo_stockly.png" class="" alt="logo" />

      </div>

      <div class="loading"></div>
    </div>
    <div id="login">
        <login-component></login-component>
      </div>

      <script src="/js/login.min.js?v=4.0.6"></script>
  </body>
</html>

    