<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/functions.php';


$mails = array_filter(glob('mails/*'), 'is_dir');
// var_dump($mails);

// exit();
?>

<!DOCTYPE html>
<html lang="en" >

<head>
  <meta charset="UTF-8">
  <title>DataTable Modal Example 01</title>

  <link rel='stylesheet' href='https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css'>
  <link rel='stylesheet' href='https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'>
  <link rel="stylesheet" href="css/style.css">

</head>

<body>

  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#">DataTable with button to show modal</a>
      </div>
    </div><!-- /.container-fluid -->
  </nav>

<form id="form1" runat="server">

  <table id="example" class="display" style="width:100%">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Asunto</th>
        <th>Destinatarios</th>
        <th>Age</th>
        <th>Creado</th>
        <th>Recipientes</th>
      </tr>
    </thead>
    <tbody>

      <?php
      foreach ($mails as $key => $mail) {
        $campaign = getCampaign($mail);
        ?>

        <tr>
          <td>AAAAA</td>
          <td><?=$campaign->subject?></td>
          <td>Edinburgh</td>
          <td><button id="btnMyTest001" type="button" class="btn btn-success" data-toggle="modal" data-target="#my_modal" data-age="61">View Age</button></td>
          <td>2011/04/25</td>
          <td>$320,800</td>
        </tr>

      <?php
      }
      ?>

      
      <tr>
        <td>Michael Bruce</td>
        <td>Javascript Developer</td>
        <td>Singapore</td>
        <td><button id="btnMyTest001" type="button" class="btn btn-success" data-toggle="modal" data-target="#my_modal" data-age="29">View Age</button></td>
        <td>2011/06/27</td>
        <td>$183,000</td>
      </tr>
      <tr>
        <td>Donna Snider</td>
        <td>Customer Support</td>
        <td>New York</td>
        <td><button id="btnMyTest001" type="button" class="btn btn-success" data-toggle="modal" data-target="#my_modal" data-age="27">View Age</button></td>
        <td>2011/01/25</td>
        <td>$112,000</td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <th>Name</th>
        <th>Position</th>
        <th>Office</th>
        <th>Age</th>
        <th>Start date</th>
        <th>Salary</th>
      </tr>
    </tfoot>
  </table>

</form>

<div class="modal fade" id="my_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Sample Modal</h4>
      </div>
      <div class="modal-body">

        <div class="form-group">

          <!--<input type="text" id="username" placeholder="User Name" class="form-control"/>-->
          <!-- <div id="confirmdetails">Confirmation details go here...</div>-->
          <label for="age">Age</label>
          <input type="text" id="age" class="form-control" />

        </div>

      </div>
      <div class="modal-footer">
        <!-- onclick="cancelRecord()" -->
        <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
<script src='https://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js'></script>
<script src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'></script>

  

    <script  src="js/index.js"></script>




</body>

</html>
