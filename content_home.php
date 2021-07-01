<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

<style>
	.panel-custom {
		border-color: #39cccc;
	}

	.panel-custom>.panel-heading-custom {
		background: #39cccc;
		color: #fff;
		border-color: #39cccc;
	}

	.card-s {
		height: 100px
	}

	.hbd {
		height: 250px;
		overflow-y: scroll;
	}

	.TugasDinas {
		height: 250px;
		overflow-y: scroll;
	}

	.statistik {
		height: 570px;
		background: #fff;
	}

	.chart-container {
		position: relative;
		margin: auto;
		width: 100%;
	}

	.chart-keterangan {
		height: 240px;
		overflow-y: scroll;
	}
</style>

<?php
//Total Siswa 
$SQLJumlahSiswa = "select BDP.JumlahBDP,TBSM.JumlahTBSM,TKRO.JumlahTKRO,TITL.JumlahTITL,AKL.JumlahAKL 
					from 
						(select count(StudentID) as JumlahBDP,ProdiID,'1' as status1 from student where ProdiID='BDP') as BDP,
						(select count(StudentID) as JumlahTBSM,ProdiID,'1' as status1 from student where ProdiID='TBSM') as TBSM,
						(select count(StudentID) as JumlahTKRO,ProdiID,'1' as status1 from student where ProdiID='TKRO') as TKRO,
						(select count(StudentID) as JumlahTITL,ProdiID,'1' as status1 from student where ProdiID='TITL') as TITL,
						(select count(StudentID) as JumlahAKL,ProdiID,'1' as status1 from student where ProdiID='AKL') as AKL
					where 
						BDP.status1=TBSM.status1 and 
						BDP.status1=TKRO.status1 and
						BDP.status1=TITL.status1 and 
						BDP.status1=AKL.status1
					";
$all_result_JumlahSiswa = ew_ExecuteRow($SQLJumlahSiswa) or die("error during: " . $SQLJumlahSiswa);

$JumlahBDP = $all_result_JumlahSiswa[JumlahBDP];
$JumlahTKRO = $all_result_JumlahSiswa[JumlahTKRO];
$JumlahTBSM = $all_result_JumlahSiswa[JumlahTBSM];
$JumlahTITL = $all_result_JumlahSiswa[JumlahTITL];
$JumlahAKL = $all_result_JumlahSiswa[JumlahAKL];

?>

<!-- BARIS #1 -->
<div class="row">
	<div class="col-md-12">
		<!-- PANEL RENOP !-->
		<div class="panel panel-default panel-custom">
			<div class="panel-heading panel-heading-custom"><b>JUMLAH SISWA</b></div>
			<div class="panel-body">
				<div class="row">
					<!-- BDP !-->
					<div class="col-sm-2">
						<a class="small-box bg-yellow" href="studentlist.php?x_ProdiID=BDP&z_ProdiID=LIKE&cmd=search" target="_self" style="text-decoration:none;">
							<div class="inner card-s">
								<table width="100%">
									<tr>
										<td>
											<h3><?php echo $JumlahBDP; ?></h3>
											<h4>Siswa</h4>
										</td>

									</tr>
								</table>
							</div>
							<div class="icon">
								<i class="ion ion-bag"></i>
							</div>
							<div class="small-box-footer">BDP <i class="fa fa-arrow-circle-right"></i></div>
						</a>
					</div>
					<!-- TKRO !-->
					<div class="col-sm-2">
						<a class="small-box bg-red" href="studentlist.php?x_ProdiID=TKRO&z_ProdiID=LIKE&cmd=search" target="_self" style="text-decoration:none;">
							<div class="inner card-s">
								<table width="100%">
									<tr>
										<td>
											<h3><?php echo $JumlahTKRO; ?></h3>
											<h4>Siswa</h4>
										</td>
										<td align="right" valign="top">

										</td>
									</tr>
								</table>
							</div>
							<div class="icon">
								<i class="ion ion-bag"></i>
							</div>
							<div class="small-box-footer">TKRO <i class="fa fa-arrow-circle-right"></i></div>
						</a>
					</div>
					<!-- TBSM !-->
					<div class="col-sm-2">
						<a class="small-box bg-teal" href="studentlist.php?x_ProdiID=TBSM&z_ProdiID=LIKE&cmd=search" target="_self" style="text-decoration:none;">
							<div class="inner card-s">
								<table width="100%">
									<tr>
										<td>
											<h3><?php echo $JumlahTBSM; ?></h3>
											<h4>Siswa</h4>
										</td>
										<td align="right" valign="top">

										</td>
									</tr>
								</table>
							</div>
							<div class="icon">
								<i class="ion ion-bag"></i>
							</div>
							<div class="small-box-footer">TBSM <i class="fa fa-arrow-circle-right"></i></div>
						</a>
					</div>
					<!-- TITL !-->
					<div class="col-sm-2">
						<a class="small-box bg-green" href="studentlist.php?x_ProdiID=TITL&z_ProdiID=LIKE&cmd=search" target="_self" style="text-decoration:none;">
							<div class="inner card-s">
								<table width="100%">
									<tr>
										<td>
											<h3><?php echo $JumlahTITL; ?></h3>
											<h4>Siswa</h4>
										</td>
										<td align="right" valign="top">
											<table>

											</table>
										</td>
									</tr>
								</table>
							</div>
							<div class="icon">
								<i class="ion ion-bag"></i>
							</div>
							<div class="small-box-footer">TITL <i class="fa fa-arrow-circle-right"></i></div>
						</a>
					</div>

					<!-- AKL !-->
					<div class="col-sm-2">
						<a class="small-box bg-fuchsia" href="studentlist.php?x_ProdiID=TITL&z_ProdiID=LIKE&cmd=search" target="_self" style="text-decoration:none;">
							<div class="inner card-s">
								<table width="100%">
									<tr>
										<td>
											<h3><?php echo $JumlahAKL; ?></h3>
											<h4>Siswa</h4>
										</td>
										<td align="right" valign="top">
											<table>

											</table>
										</td>
									</tr>
								</table>
							</div>
							<div class="icon">
								<i class="ion ion-bag"></i>
							</div>
							<div class="small-box-footer">AKL <i class="fa fa-arrow-circle-right"></i></div>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>