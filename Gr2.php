<?php
	require_once("/home/pi/icalendar-master/zapcallib.php");
	$server = "localhost";
	$user = "admin";
	$passwd = "nWd3cOhlXXGbV4i9V7yJ";
	$dbase = "rapla";

	$icalobj = new ZCiCal();

	$conn = new mysqli($server, $user, $passwd, $dbase);

	if ($conn->connect_error) {
		die("connection failed: ". $conn->connect_error);
	}

	$timezone = new ZCiCalNode("VTIMEZONE", $icalobj->curnode);
	$timezone->addNode(new ZCiCalDataNode("TZID:Europe/Berlin"));
        $timezone->addNode(new ZCiCalDataNode("X-LIC-LOCATION:Europe/Berlin"));

	$daylight = new ZCiCalNode("DAYLIGHT", $timezone);
	$daylight->addNode(new ZCiCalDataNode("TZOFFSETFROM:+0100"));
        $daylight->addNode(new ZCiCalDataNode("TZOFFSETTO:+0200"));
        $daylight->addNode(new ZCiCalDataNode("TZNAME:CEST"));
        $daylight->addNode(new ZCiCalDataNode("DTSTART:19700329T020000"));
        $daylight->addNode(new ZCiCalDataNode("RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU"));

	$standard = new ZCiCalNode("STANDARD", $timezone);
        $standard->addNode(new ZCiCalDataNode("TZOFFSETFROM:+0200"));
        $standard->addNode(new ZCiCalDataNode("TZOFFSETTO:+0100"));
        $standard->addNode(new ZCiCalDataNode("TZNAME:CET"));
        $standard->addNode(new ZCiCalDataNode("DTSTART:19701025T030000"));
        $standard->addNode(new ZCiCalDataNode("RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU"));


	$eventnumber = 0;
	$sql = "SELECT * FROM eventsG2";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$title = $row["title"];
			${"event".$eventnumber} = new ZCiCalNode("VEVENT", $icalobj->curnode);
			${"event".$eventnumber}->addNode(new ZCiCalDataNode("SUMMARY:{$row['title']}"));
			${"event".$eventnumber}->addNode(new ZCiCalDataNode("DTSTART:".ZCiCal::fromSqlDateTime($row["time_start"])));
			${"event".$eventnumber}->addNode(new ZCiCalDataNode("DTEND:".ZCiCal::fromSqlDateTime($row["time_end"])));
			${"event".$eventnumber}->addNode(new ZCiCalDataNode("LOCATION:{$row['room']}"));
			${"event".$eventnumber}->addNode(new ZCiCalDataNode("DESCRIPTION:{$row['reader']}"));
			$eventnumber++;
		}
	}
	echo $icalobj->export();
?>
