<?php
class ControllerExtensionModuleExporter extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('extension/module/exporter');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('exporter', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/exporter', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/module/exporter', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		if (isset($this->request->post['exporter_status'])) {
			$data['exporter_status'] = $this->request->post['exporter_status'];
		} else {
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/exporter', $data));
		
	}
	
	//creates the excel sheet, using data from the database, that will be exported as an excel 2007 sheet
	public function export()
	{
		error_reporting(E_ALL);
		ini_set('display_errors', TRUE);
		ini_set('display_startup_errors', TRUE);
		date_default_timezone_set('Europe/London');
		  
		require_once(DIR_SYSTEM . 'library/PHPExcel.php');
		require_once(DIR_SYSTEM . 'library/PHPExcel/Writer/Excel2007.php');

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("")
									 ->setLastModifiedBy("")
									 ->setTitle("ExportedInformation")
									 ->setSubject("ExportedInformation")
									 ->setDescription("Exported Excel Sheet Getting Data from the Database")
									 ->setKeywords("")
									 ->setCategory("");
									 
		$this->load->model('extension/module/exporter');
		$query = $this->model_extension_module_exporter->getResults();
		$rowNum = 2;
		
		$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "Email")
            ->setCellValue('B1', "First Name")
            ->setCellValue('C1', "Last Name")
            ->setCellValue('D1', "Product")
			->setCellValue('E1', "Category");
			
		foreach($query->rows as $result){
         $email = $result['email'];
         $firstname = $result['firstname'];
		 $lastname = $result['lastname'];
		 $name = $result['name'];
		 $type = $result['type'];
		 
		 $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$rowNum, $email)
            ->setCellValue('B'.$rowNum, $firstname)
            ->setCellValue('C'.$rowNum, $lastname)
            ->setCellValue('D'.$rowNum, $name)
			->setCellValue('E'.$rowNum, $type);
		$rowNum++;
		}
					 					 
		$objPHPExcel->getActiveSheet()->setTitle('ExportedData');

		$objPHPExcel->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="\ExportInfo.xlsx\"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

		$this->response->redirect($this->url->link('catalog/download', 'token=' . $this->session->data['token'] . $url, true));
	}
	
	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/exporter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install()
	{
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('exporter', ['exporter_status' => 1]);
		
		//enables the modification if the module is installed
		$this->load->model('extension/module/exporter');
		$this->model_extension_module_exporter->enableMod();
	}

	public function uninstall()
	{
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('exporter');
		
		//disables the modification if the module is removed
		$this->load->model('extension/module/exporter');
		$this->model_extension_module_exporter->disableMod();
	}
	
}
