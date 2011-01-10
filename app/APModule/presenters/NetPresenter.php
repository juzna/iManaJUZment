<?php

namespace APModule;


class NetPresenter extends BasePresenter {
	function actionPing() {
    $params = $this->getRequest()->getPost();
    if(empty($params['ip'])) return;

    $ip = $params['ip'];
		
		ob_end_clean();
		ob_implicit_flush(true);

		echo '<pre>';
		echo "<h2>Pingam $ip...</h2>";


		// Pingame
		$limit = @$_GET['limit'] or $limit = 5;
		$cmd = "ping -c $limit -w 5000 $ip";
		$fp = popen($cmd, 'r');
		
		$received = 0;
		for($x = 1; $ret = fgets($fp, 100); $x++) {
			$ret = trim($ret);
			if(connection_aborted()) exit();

			// Pokud je to uspesnej radek
			if(strpos($ret, ' bytes from ')) {
				$received++;
			
        // Normalka vypis
        printf("%4d: %s\n", $x, $ret);
			}
		}
		fclose($fp);


		// Vysledek
		$ok = round($received / $limit * 5);
		echo "<b>OK: $received / $limit</b>";
		$color = array(0=>'red', 1=>'#dd0000', 2=>'#990000', 3=> '#003300', 4=> '#009900', 5=>'#00ff00');
		$color = @$color[$ok];
		echo('<script language="javascript">window.document.body.style.background = "'.$color.'";</script>');
		
		// Konec
		echo("<h2>Finished</h2>");
		exit();
	}

	function actionTracert() {
		$ip = trim(@$_GET['ip']);

		@ob_end_flush();
		ob_implicit_flush(true);
		echo '<pre>';

		$cmd = "traceroute -ln -q 2 -w 2 -m 10 $ip";
		passthru($cmd);

		echo '<br /><b>Konec</b>';
		exit();


	}

}
