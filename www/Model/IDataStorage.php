<?php
//��������� ��� ���� ������� ��������������� ��� ������� �� ��
	interface IDataStorage {
		
		//�������� ������ �� ����������� � ��
		public function setConnection($connection);
		
		//����� ��������� ��� ������� ������ �� �� � ������� JSON
		function selectDataToJSON($requestParam);
		
		//����� ��������� ��� �������� ������ � ������� JSON ��� ������� � ��
		public function insertJSONDataToBD($sourceName,$requestParam);
	
	}
	
?>