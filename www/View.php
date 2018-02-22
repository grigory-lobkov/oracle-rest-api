<?php
	require_once('HTTPConstant.php');
	require_once('Messages.php');
	
	/*����� ���������� ������ ��� ������ ���������� ��� ������������� � ��������*/
	class View {
		
		//������� ��� �������� ������ �� ������� � �������
		function sendResponse($headerCode,$respText){
			$sendHeader=HTTPConstant::$HTTPPrefHeader;
			switch ($headerCode) {
			    case HTTPConstant::$badRequestCode:
			        $sendHeader+=
			    		' '+HTTPConstant::$badRequestCode
			    		+' '+HTTPConstant::$badRequestMessage;
			        break;
			    case HTTPConstant::$unauthorizedCode:
			        $sendHeader+=
			    		' '+HTTPConstant::$unauthorizedCode
			    		+' '+HTTPConstant::$unauthorizedMessage;
			        break;
			    case HTTPConstant::$notAcceptableCode:
			        $sendHeader+=
			    		' '+HTTPConstant::$notAcceptableCode
			    		+' '+HTTPConstant::$notAcceptableMessage;
			        break;
			    case HTTPConstant::$okCode:
			        $sendHeader+=
			    		' '+HTTPConstant::$okCode
			    		+' '+HTTPConstant::$okMessage;
			        break;
				default://��������� ��������� ��� ������������ ���� ������
					die(Messages::$unknowHTTPHeaderErr);
			}
			header($sendHeader);
			//die(json_encode($respText));
			echo json_encode($respText);
			echo ' // <br/><br/>';
			var_dump($respText);
			die();
		}
		
	}
?>