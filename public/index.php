<?php 
// @see application/bootstrap.php
$bootstrap = true; 
require '../application/bootstrap.php';  

// $frontController is created in your boostrap file. Now we'll dispatch it, which dispatches your application. 
try {
    $frontController->dispatch();
} catch (Exception $e) {
	error_log($e->getMessage());
	
	$hint = null;
	switch (get_class($e)) {
		case 'Zend_Db_Statement_Exception':
			$trace = $e->getTrace();
			$hint = $trace[2]['args'][0];
			break;
		case 'PDOException':
			$trace = $e->getTrace();
			$hint = $trace[0]['args'][0];
			break;
	}
	
	echo '<!-- ERROR -->';
	echo '<style type="text/css">';
	echo '#mainPanelError {';
	echo '  text-align:left;';
	echo '  padding:10px;';
	echo '  background:#ffea50;';
	echo '  border:4px double red;';
	echo '}';
	echo '#stackTrace, .hint {';
	echo '  text-align:left;';
	echo '  background:white;';
	echo '  border:1px solid black;';
	echo '  margin:30px;';
	echo '}';
	echo '.stackTraceElement {';
	echo '  text-align:left;';
	echo '  background:white;';
	echo '  margin:0 0 5px 0;';
	echo '}';
	echo '.hint {';
	echo '  color:red;';
	echo '  font-weight:bold;';
	echo '  padding:10px;';
	echo '}';
	echo '.fileInfo {';
	echo '  font-size:0.8em;';
	echo '  color:#999999;';
	echo '  margin:0 0 0 15px;';
	echo '}';
	echo '</style>';
	echo '<div id="mainPanelError"><strong>';
	echo get_class($e);
	echo '<br />';
	echo $e->getMessage();
	echo '</strong>';
	$trace = $e->getTrace();
	if ($trace) {
		function getInfo($traceElt, $param = false) {
			
			$info = array_key_exists('class', $traceElt) ? $traceElt['class'] . $traceElt['type'] : '';
			$info .= $traceElt['function'];
			$info .= '(';
			if ($traceElt['args']) {
				$count = count($traceElt['args']);
				for ($i = 0; $i < $count; $i++) {
					$arg = $traceElt['args'][$i];
					$info .= is_object($arg) ? get_class($arg) : gettype($arg);						
					if ($i < $count - 1) {
						$info .= ', ';
					}
				}
			}
			$info .= ')';
			if ($traceElt['args'] && $param) {
				$info .= __($traceElt['args'], 'func_get_args(): ', false);
			} 
			return $info;
		}
		if ($hint) {
			echo '<div class="hint">'.$hint.'</div>';
		}
		echo '<br /><em>in '.getInfo($trace[0], $hint === null).'</em>';
		echo '<div id="stackTrace">';
		echo '<b>StackTrace :</b><br /><br />';
		foreach ($trace as $k => $line) {
			echo '<div class="stackTraceElement">';
			$callInfo = ($k < count($trace) - 1) ? getInfo($trace[$k + 1]) : "";
			echo $callInfo;
			if (array_key_exists('file', $line)) {
				echo ' (' .basename($line['file']) .':'.$line['line'].')<br />';
				echo '<span class="fileInfo">in file '.$line['file'].'</span>';
			}
			echo '</div>';
		}
		echo '<div>';
	}
	echo '</div>';
} 


