<!DOCTYPE html>
<html lang="de" data-cookies-popup="false">
<?php
$serverName2 = "SI0VMC1012\MSSQL2014EX_01"; // serverName\instanceName
$connectionInfo2 = array(
    "Database" => "dbFIFORegal",
    "UID" => "adminMSSQL2014EX_01",
    "PWD" => "adminMSSQL2014EX_01%"
);
$conn2 = sqlsrv_connect($serverName2, $connectionInfo2);
// //2. Array Generierung -----------------------------------------------------------1--------------------------

// Array erstellen, mit allen KLTs, die im Regal liegen
$tsql = "SELECT *
FROM Teile
WHERE sqlBestand='1'";
$stmt = sqlsrv_query($conn2, $tsql);
$kltsimregal = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $kltsimregal[] = array(
        "PSID" => $row['PSID'],
        "Anzahl" => $row['sqlBehaelterzahl'],
        "TTNR" => $row['sqlSachnummer'],
        "Ruestklasse" => $row['sqlRuestklasse'],
        "zeit" => $row['sqlLetztesEinlagern'],
        "Bearbeitungszeit" => $row['sqlBearbeitungszeit']
    );
}

// Array erstellen der Status Tabelle

$tsql = "SELECT *
FROM Status";
$stmt = sqlsrv_query($conn2, $tsql);
$status = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    
    $status[] = array(
        "summeTeile13" => $row['summeTeile13'],
        "summeBearbeitung13" => $row['summeBearbeitung13'],
        "summeBearbeitung17" => $row['summeBearbeitung17'],
        "summeTeile17" => $row['summeTeile17'],
        "summeBearbeitung27" => $row['summeBearbeitung27'],
        "summeTeile27" => $row['summeTeile27'],
        "summeBearbeitungGesamt" => $row['summeBearbeitungGesamt'],
        "summeTeileGesamt" => $row['summeTeileGesamt'],
        "status13" => $row['Status13'],
        "status17" => $row['Status17'],
        "status27" => $row['Status27'],
        "Fertigungsempfehlung" => $row['Fertigungsempfehlung'],
        "NaechsteRuestklasse" => $row['NaechsteRuestklasse'],
        "AktuelleRuestklasse" => $row['AktuelleRuestklasse']
    );
}

// Array erstellen mit dem LOG

$tsql = "SELECT *
FROM Log WHERE sqlVorgang='Auslagern'";
$stmt = sqlsrv_query($conn2, $tsql);
$log_auslagern = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    
    $log_auslagern[] = array(
        "PSID" => $row['PSID'],
        "TTNR" => $row['sqlTTNR'],
        "Ruestklasse" => $row['sqlRuestklasse'],
        "Einlagern" => $row['sqlEinlagern'],
        "Auslagern" => $row['sqlAuslagern'], 
        "Dauer" => 0, 
        "Status" => "",
        "TagID" => $row['sqlTagID']       
    );
}

// Array mit dem Fertigungsprogramm erstellen (Nächste 5 KLTs)
$tsql = "SELECT *
         FROM Fertigungsprogramm";
$stmt = sqlsrv_query($conn2, $tsql);
$fertigungsprogramm = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $fertigungsprogramm[] = array(
        "PSID" => $row['PSID'],
        "TTNR" => $row['sqlTTNR'],
        "Ruestklasse" => $row['sqlRuestklasse'],
        "Bearbeitungszeit" => $row['sqlBearbeitungszeit'],
        "zeit" => $row['sqlLetztesEinlagern'],
        "Fruehstefertigstellung" => "",
        "Restzeit" => ""
    );
}

// Array erstellen 13er, sortiert nach Einlagerzeit
$tsql = "SELECT *
FROM Teile
WHERE sqlBestand='1' AND sqlRuestklasse='13'
ORDER BY sqlLetztesEinlagern ASC;";
$stmt = sqlsrv_query($conn2, $tsql);
$kltsimregal13 = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $kltsimregal13[] = array(
        "PSID" => $row['PSID'],
        "Anzahl" => $row['sqlBehaelterzahl'],
        "TTNR" => $row['sqlSachnummer'],
        "Ruestklasse" => $row['sqlRuestklasse'],
        "zeit" => $row['sqlLetztesEinlagern'],
        "Bearbeitungszeit" => $row['sqlBearbeitungszeit']
    );
}
// Array erstellen 17er, sortiert nach Einlagerzeit
$tsql = "SELECT *
FROM Teile
WHERE sqlBestand='1' AND sqlRuestklasse='17'
ORDER BY sqlLetztesEinlagern ASC;";
$stmt = sqlsrv_query($conn2, $tsql);
$kltsimregal17 = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $kltsimregal17[] = array(
        "PSID" => $row['PSID'],
        "Anzahl" => $row['sqlBehaelterzahl'],
        "TTNR" => $row['sqlSachnummer'],
        "Ruestklasse" => $row['sqlRuestklasse'],
        "zeit" => $row['sqlLetztesEinlagern'],
        "Bearbeitungszeit" => $row['sqlBearbeitungszeit']
    );
}

// Array erstellen 27er, sortiert nach Einlagerzeit
$tsql = "SELECT *
FROM Teile
WHERE sqlBestand='1' AND sqlRuestklasse='27'
ORDER BY sqlLetztesEinlagern ASC;";
$stmt = sqlsrv_query($conn2, $tsql);
$kltsimregal27 = array();
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $kltsimregal27[] = array(
        "PSID" => $row['PSID'],
        "Anzahl" => $row['sqlBehaelterzahl'],
        "TTNR" => $row['sqlSachnummer'],
        "Ruestklasse" => $row['sqlRuestklasse'],
        "zeit" => $row['sqlLetztesEinlagern'],
        "Bearbeitungszeit" => $row['sqlBearbeitungszeit']
    );
}
$zaehler=count($log_auslagern);
if ($zaehler>100){
    $zaehler=100;
}

for ($i=0; $i<$zaehler; $i++){
    $timestamp_einlagern = date_create_from_format('Y/m/d - H#i#s', $log_auslagern[$i]['Einlagern']);
    $timestamp_einlagern = date_add($timestamp_einlagern, date_interval_create_from_date_string('1 hours')); // an Zeitzone anpassen
    $timestamp_auslagern = date_create_from_format('Y/m/d - H#i#s', $log_auslagern[$i]['Auslagern']);
    $timestamp_auslagern = date_add($timestamp_auslagern, date_interval_create_from_date_string('1 hours')); // an Zeitzone anpassen   
    $temp1 = strtotime(date_format($timestamp_einlagern, 'Y-m-d H:i:s')); // Sekunden seit 01.01.1970, UNIX-Standard
    $temp2 = strtotime(date_format($timestamp_auslagern, 'Y-m-d H:i:s'));    
    $zeitdifferenz = abs($temp2 - $temp1);
    if($zeitdifferenz<50400){
        $log_auslagern[$i]['Status']="ok";
    }
    else  if ($zeitdifferenz<57600 && $zeitdifferenz>50400){
        $log_auslagern[$i]['Status']="kritisch";
    }        
    else {
        $log_auslagern[$i]['Status']="ueberschritten";
    }    
    $log_auslagern[$i]['Dauer']= intval($zeitdifferenz / 3600) . "h " . intval(($zeitdifferenz % 3600) / 60) . "min";
    $log_auslagern[$i]['Einlagern']= date("d.m.Y - H:i", $temp1);
    $log_auslagern[$i]['Auslagern']= date("d.m.Y - H:i", $temp2);    
    
    
}
$summeBearbeitung13 = intval($status[0]['summeBearbeitung13'] / 3600) . "h " . intval(($status[0]['summeBearbeitung13'] % 3600) / 60) . "min";
$summeBearbeitung17 = intval($status[0]['summeBearbeitung17'] / 3600) . "h " . intval(($status[0]['summeBearbeitung17'] % 3600) / 60) . "min";
$summeBearbeitung27 = intval($status[0]['summeBearbeitung27'] / 3600) . "h " . intval(($status[0]['summeBearbeitung27'] % 3600) / 60) . "min";
$summeBearbeitungGesamt = intval($status[0]['summeBearbeitungGesamt'] / 3600) . "h " . intval(($status[0]['summeBearbeitungGesamt'] % 3600) / 60) . "min";

$auslastung13prozent = intval($status[0]['summeTeile13'] / 6 * 100);
$auslastung17prozent = intval($status[0]['summeTeile17'] / 6 * 100);
$auslastung27prozent = intval($status[0]['summeTeile27'] / 6 * 100);
?>

<head>
<!-- Meta -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<!-- Title -->
<title>RFID FIFO Regal - Detailansicht</title>
<!-- Google Web Fonts -->
<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700'
	rel='stylesheet' type='text/css'>
<link
	href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,600'
	rel='stylesheet' type='text/css'>
<!-- CSS Styles -->
<link rel="stylesheet" href="assets/css/styles.css" />
<!-- CSS Base -->
<link id="theme" rel="stylesheet"
	href="assets/css/themes/theme-classic.css" />
</head>
<body>
	<!-- Loader -->
	<div id="page-loader">
		<svg class="loader-1 loader-primary" width="65px" height="65px"
			viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
            <circle class="circle" fill="none" stroke-width="3"
				stroke-linecap="round" cx="33" cy="33" r="30"></circle>
         </svg>
	</div>
	<!-- Loader / End -->
	<!-- Content -->

	<div id="content">
		<div class="fifo_teamleiter">
			<section>

				<div class="container-fluid">
					<h1 class="apptitel_teamleiter">RFID Supermarkt - Detailansicht</h1>
					<div class="row">
						<div class="col-md-12">
							<div class="col-md-3">
								<div class="row" style="padding-bottom: 10px;">
									<div class="ruesttypkachel">
								<?php
        
        if ($status[0]['AktuelleRuestklasse'] == 13) {
            echo "<div class='col-md-10 kachel_FT13'>";
        } else if ($status[0]['AktuelleRuestklasse'] == 17) {
            echo "<div class='col-md-10 kachel_FT17'>";
        } else if ($status[0]['AktuelleRuestklasse'] == 27) {
            echo "<div class='col-md-10 kachel_FT27'>";
        }
        ?>
									
										
										<p class="ruesttyp">
										<?php
        
        echo "SW" . $status[0]['AktuelleRuestklasse'];
        ?>
										</p>
										<br>
										<p class="ruesttyplabel">Aktuelle R&uuml;stklasse</p>
									</div>
								</div>
							</div>
							<div class="row" style="padding-bottom: 10px;">
								<div class="ruesttypkachel">
								<?php
        
        if ($status[0]['NaechsteRuestklasse'] == 13) {
            echo "<div class='col-md-10 kachel_FT13'>";
        } else if ($status[0]['NaechsteRuestklasse'] == 17) {
            echo "<div class='col-md-10 kachel_FT17'>";
        } else if ($status[0]['NaechsteRuestklasse'] == 27) {
            echo "<div class='col-md-10 kachel_FT27'>";
        }
        ?>
									
										<p class="ruesttyp">
										<?php
        echo "SW" . $status[0]['NaechsteRuestklasse'];
        ?>
										</p>
									<br>
									<p class="ruesttyplabel">N&auml;chste R&uuml;stklasse</p>
								</div>
							</div>
						</div>

					</div>

					<div class="col-md-9">
						<div class="row">
							<!-- Nav tabs -->
							<div class="tab_navigation">
								<ul class="nav nav-pills mb-30" role="tablist">

									<li class="active"><a href="#aktuell" role="tab"
										data-toggle="tab">Aktuell</a></li>
									<li><a href="#historie" role="tab" data-toggle="tab">HISTORIE</a></li>



								</ul>
							</div>
							<!-- Tab panes -->
							<div class="tab-content">

								<div role="tabpanel" class="tab-pane active fade in"
									id="aktuell">
									<!-- 										<div class="col-md-4"> -->
									<table class="table table-hover">
										<tr>
											<td class="infotitel"><b>KLTs gesamt:</b><br> <b>Bearbeitungszeit:</b></td>
											<td class="infocontent" id="summeTeileGesamt">
												<?php
            echo "<b>" . $status[0]['summeTeileGesamt'] . "<br>" . $summeBearbeitungGesamt . "</b>";
            ?>
												</td>
											<td class="status_bar">
												<div class="col-lg-8 col-lg-push-2">

													<ul class="process-steps"><?php
            
            if (($status[0]['status13'] == "kritisch" || $status[0]['status17'] == "kritisch" || $status[0]['status27'] == "kritisch") && ($status[0]['status13'] != "ueberschritten" && $status[0]['status17'] != "ueberschritten" && $status[0]['status27'] != "ueberschritten")) {
                echo "
                            <li class='step' >WBZ i.o.!</li>
                           <li class='step active kritisch' >WBZ kritisch!</li>
                            <li class='step' >WBZ &uuml;berschritten!</li>";
            } else if ($status[0]['status13'] == "ueberschritten" || $status[0]['status17'] == "ueberschritten" || $status[0]['status27'] == "ueberschritten") {
                echo "
                            <li class='step' >WBZ i.o.!</li>
                           <li class='step' >WBZ kritisch!</li>
                           <li class='step active ueberschritten' >WBZ &uuml;berschritten!</li>";
            } else {
                echo "
                            <li class='step active io' >WBZ i.o.!</li>
                           <li class='step' >WBZ kritisch!</li>
                            <li class='step' >WBZ &uuml;berschritten!</li>";
            }
            ?>
                                          
                        
                        </ul>
												</div>
											</td>
										</tr>
										<!-- 												<tr> -->
										<!-- 													<td class="infotitel">Bearbeitungszeit:</td> -->
										<!-- 													<td class="infocontent" id="summeBearbeitungGesamt"> -->
												<?php
            // echo $summeBearbeitungGesamt;
            // ?>
<!-- 												</td> -->
										<!-- 												</tr> -->
										<tr class="leerzeile">
											<td class="infotitel"></td>
											<td class="infocontent"></td>
											<td class="status_bar"></td>
										</tr>
										<tr>
											<td class="infotitel">13er KLTs:<br>Bearbeitungszeit:
											</td>
											<td class="infocontent" id="summeTeile13">
												<?php
            echo $status[0]['summeTeile13'] . "<br>" . $summeBearbeitung13;
            ?>
												</td>
											<td class="auslastung_bar  status_bar" id="summeTeileGesamt"
												style="vertical-align: middle;">
												<div class="progress">
                                                                                          
                                                <?php
                                                
                                                if ($status[0]['summeTeile13'] > 50) {
                                                    echo "<div class='auslastung_kritisch'>";
                                                } else {
                                                    echo "<div class='auslastung_normal'>";
                                                }
                                                echo "<div class='progress-bar' role='progressbar' aria-valuenow='" . $auslastung13prozent . "' aria-valuemin='0' aria-valuemax='100' style='width: " . $auslastung13prozent . "%;'>" . $auslastung13prozent . "% - " . $status[0]['summeTeile13'] . " / 6 KLTs</div></div>";
                                                ?>
                                             
            									</div>
											</td>
										</tr>

										<tr class="leerzeile">
											<td class="infotitel"></td>
											<td class="infocontent"></td>
											<td class="status_bar"></td>
										</tr>
										<tr>
											<td class="infotitel">17er KLTs:<br>Bearbeitungszeit:
											</td>
											<td class="infocontent" id="summeTeile17">
												<?php
            echo $status[0]['summeTeile17'] . "<br>" . $summeBearbeitung17;
            ?>
												</td>
											<td class="auslastung_bar  status_bar" id="summeTeileGesamt"
												style="vertical-align: middle;">
												<div class="progress">
                                                                                          
                                                <?php
                                                
                                                if ($status[0]['summeTeile17'] > 50) {
                                                    echo "<div class='auslastung_kritisch'>";
                                                } else {
                                                    echo "<div class='auslastung_normal'>";
                                                }
                                                echo "<div class='progress-bar' role='progressbar' aria-valuenow='" . $auslastung17prozent . "' aria-valuemin='0' aria-valuemax='100' style='width: " . $auslastung17prozent . "%;'>" . $auslastung17prozent . "% - " . $status[0]['summeTeile17'] . " / 6 KLTs</div></div>";
                                                ?>
                                             
            									</div>
											</td>
										</tr>

										<tr class="leerzeile">
											<td class="infotitel"></td>
											<td class="infocontent"></td>
											<td class="status_bar"></td>
										</tr>
										<tr>
											<td class="infotitel">27er KLTs:<br>Bearbeitungszeit:
											</td>
											<td class="infocontent" id="summeTeile27">
												<?php
            echo $status[0]['summeTeile27'] . "<br>" . $summeBearbeitung27;
            ?>
												</td>
											<td class="auslastung_bar  status_bar" id="summeTeileGesamt"
												style="vertical-align: middle;">
												<div class="progress">
                                                                                          
                                                <?php
                                                
                                                if ($status[0]['summeTeile27'] > 50) {
                                                    echo "<div class='auslastung_kritisch'>";
                                                } else {
                                                    echo "<div class='auslastung_normal'>";
                                                }
                                                echo "<div class='progress-bar' role='progressbar' aria-valuenow='" . $auslastung27prozent . "' aria-valuemin='0' aria-valuemax='100' style='width: " . $auslastung27prozent . "%;'>" . $auslastung27prozent . "% - " . $status[0]['summeTeile27'] . " / 6 KLTs</div></div>";
                                                ?>
                                             
            									</div>
											</td>
										</tr>
										<tr class="leerzeile">
											<td class="infotitel"></td>
											<td class="infocontent"></td>
											<td class="status_bar"></td>
										</tr>


									</table>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="historie">

									<!-- Accordion -->
									<div class="panel-group" role="tablist"
										aria-multiselectable="true">
										<!-- Panel -->
										<div class="panel panel-default">
											<div class="panel-heading" role="tab">
												<h3 class="panel-title">
													<a data-toggle="collapse" href="#collapseOne"
														aria-expanded="true">Aktuelle Schicht</a>
												</h3>
											</div>
											<div id="collapseOne" class="panel-collapse collapse"
												role="tabpanel">
												<div class="panel-body">
													<table>
														<tr>
															<td class="infotitel">KLTs gesamt:</td>
															<td class="infocontent" id="summeTeileGesamt">5</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitungGesamt">5h
																16min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">13er KLTs:</td>
															<td class="infocontent" id="summeTeile13">0</td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung13">0h 0min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">17er KLTs:</td>
															<td class="infocontent" id="summeTeile17">4</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung17">4h 17min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">27er KLTs:</td>
															<td class="infocontent" id="summeTeile27">1</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung27">0h 59min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
													</table>
												</div>
											</div>
										</div>
										<!-- Panel -->
										<div class="panel panel-default">
											<div class="panel-heading" role="tab">
												<h3 class="panel-title">
													<a data-toggle="collapse" href="#collapseTwo"
														aria-expanded="false">Fr&uuml;hschicht gestern</a>
												</h3>
											</div>
											<div id="collapseTwo" class="panel-collapse collapse"
												role="tabpanel">
												<div class="panel-body">
													<table>
														<tr>
															<td class="infotitel">KLTs gesamt:</td>
															<td class="infocontent" id="summeTeileGesamt">5</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitungGesamt">5h
																16min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">13er KLTs:</td>
															<td class="infocontent" id="summeTeile13">0</td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung13">0h 0min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">17er KLTs:</td>
															<td class="infocontent" id="summeTeile17">4</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung17">4h 17min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">27er KLTs:</td>
															<td class="infocontent" id="summeTeile27">1</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung27">0h 59min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
													</table>
												</div>
											</div>
										</div>
										<!-- Panel -->
										<div class="panel panel-default">
											<div class="panel-heading" role="tab">
												<h3 class="panel-title">
													<a data-toggle="collapse" href="#collapseThree"
														aria-expanded="false">Sp&auml;tschicht gestern</a>
												</h3>
											</div>
											<div id="collapseThree" class="panel-collapse collapse"
												role="tabpanel">
												<div class="panel-body">
													<table>
														<tr>
															<td class="infotitel">KLTs gesamt:</td>
															<td class="infocontent" id="summeTeileGesamt">5</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitungGesamt">5h
																16min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">13er KLTs:</td>
															<td class="infocontent" id="summeTeile13">0</td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung13">0h 0min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">17er KLTs:</td>
															<td class="infocontent" id="summeTeile17">4</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung17">4h 17min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">27er KLTs:</td>
															<td class="infocontent" id="summeTeile27">1</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung27">0h 59min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
													</table>
												</div>
											</div>
										</div>
										<div class="panel panel-default">
											<div class="panel-heading" role="tab">
												<h3 class="panel-title">
													<a data-toggle="collapse" href="#collapseFour"
														aria-expanded="false">Nachtschicht gestern</a>
												</h3>
											</div>
											<div id="collapseFour" class="panel-collapse collapse"
												role="tabpanel">
												<div class="panel-body">
													<table>
														<tr>
															<td class="infotitel">KLTs gesamt:</td>
															<td class="infocontent" id="summeTeileGesamt">5</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitungGesamt">5h
																16min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">13er KLTs:</td>
															<td class="infocontent" id="summeTeile13">0</td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung13">0h 0min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">17er KLTs:</td>
															<td class="infocontent" id="summeTeile17">4</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung17">4h 17min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
														<tr>
															<td class="infotitel">27er KLTs:</td>
															<td class="infocontent" id="summeTeile27">1</td>
														</tr>
														<tr>
															<td class="infotitel">Bearbeitungszeit:</td>
															<td class="infocontent" id="summeBearbeitung27">0h 59min</td>
														</tr>
														<tr class="leerzeile">
															<td class="infotitel"></td>
															<td class="infocontent"></td>
														</tr>
													</table>
												</div>
											</div>
										</div>
										<!-- Panel -->
										<div class="panel panel-default">
											<div class="panel-heading" role="tab">
												<h3 class="panel-title">
													<a data-toggle="collapse" href="#collapseFive"
														aria-expanded="false">Gesamt&uuml;berblick</a>
												</h3>
											</div>
											<div id="collapseFive" class="panel-collapse collapse"
												role="tabpanel">
												<div class="panel-body">
													<div class="col-md-7">
														<table class="sortable table table-hover" id="anyid">
															<tr>
																<th>Nr.</th>
																<th>TTNR.</th>
																<th>R&uuml;stklasse</th>
																<th>Dauer</th>
																<th>Status</th>

															</tr>
<?php

for ($i = 0; $i < $zaehler; $i ++) {
   $j=$i+1;
    if ($log_auslagern[$i]['Status']=="ueberschritten") {
        $status = "<img src='/fifo/assets/img/status_ueberschritten.png' width='25px;'>";
    } else if ($log_auslagern[$i]['Status']=="kritisch") {
        $status = "<img src='/fifo/assets/img/status_kritisch.png' width='25px;'>";
    }
    else 
    {
        $status = "<img src='/fifo/assets/img/status_io.png' width='25px;'>";
    }
    echo "<tr><td>" . $j . "</td><td>" . $log_auslagern[$i]['TTNR'] . "</td><td>" . $log_auslagern[$i]['Ruestklasse'] . "</td><td>" . $log_auslagern[$i]['Dauer'] . "</td><td>" . $status . "</td></tr>";
}
?>

</table>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>

								<div role="tabpanel" class="tab-pane fade" id="ueberblick">
									<table class="sortable" id="anyid">
										<tr>
											<th>Nr.</th>
											<th>TTNR.</th>
											<th>R&uuml;stklasse</th>
											<th>Bearbeitungsdauer</th>
											<th>KLTs im Kreislauf</th>
											<th>Einlagerzeitpunkt</th>
										</tr>
<?php

for ($i = 0; $i < count($kltsimregal); $i ++) {
    $j = $i + 1;
    echo "<tr><td>" . $j . "</td><td>" . $kltsimregal[$i]['TTNR'] . "</td><td>" . $kltsimregal[$i]['Ruestklasse'] . "</td><td>" . intval($kltsimregal[$i]['Bearbeitungszeit'] / 60) . " min</td><td>" . $kltsimregal[$i]['Anzahl'] . "</td><td>" . $kltsimregal[$i]['zeit'] . "</td></tr>";
}
?>

</table>

								</div>
								<div role="tabpanel" class="tab-pane fade" id="fifo-kapazitaet"></div>
							</div>
						</div>
					</div>
				</div>
		
		</div>

	</div>

	</div>

	</div>
	</section>
	</div>
	<!-- Content / End -->
	<footer></footer>
	<!-- Footer / End -->
	<!-- JS Libraries -->
	<script src="assets/js/jquery-1.12.3.min.js"></script>
	<!-- JS Plugins -->
	<script src="assets/js/plugins.js"></script>
	<!-- JS Core -->
	<script src="assets/js/core.js"></script>
	<script src="assets/js/sortable1.js"></script>
</body>
</html>