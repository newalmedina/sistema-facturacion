<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title></title>
	<!-- DataTables -->
  <link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
 
	<!-- DataTables -->
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
</head>
<body>
	<table id="tabla" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Rendering engine</th>
                  <th>Browser</th>
                  <th>Platform(s)</th>
                  <th>Engine version</th>
                  <th>CSS grade</th>
                </tr>
                </thead>
                <tbody>
                </tbody>	
            </table>
            <script>
            	$(document).ready( function () {
    $('#tabla').DataTable();
} );
            </script>
</body>
</html>