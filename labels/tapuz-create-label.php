<?php
/**
 * Create PDF Label with barcode
 */
require_once dirname( dirname( __FILE__ ) ) . '/vendors/TCPDF/tcpdf.php';

class Tapuz_create_label {

	private $tapuz_labels_name = array();

	private $pdf;

	private $ship_data;

	private $barcode_style = array(
		'position'     => 'C',
		'align'        => 'C',
		'stretch'      => false,
		'fitwidth'     => true,
		'cellfitalign' => '',
		'border'       => true,
		'hpadding'     => 'auto',
		'vpadding'     => 'auto',
		'fgcolor'      => array( 0, 0, 0 ),
		'bgcolor'      => false, //array(255,255,255),
		'text'         => true,
		'font'         => 'helvetica',
		'fontsize'     => 8,
		'stretchtext'  => 4
	);

	/**
	 * UTF-8 label titles
	 * Tapuz_create_label constructor.
	 *
	 * @param array $ship_data
	 */
	public function __construct(array $ship_data) {

		$this->tapuz_labels_name['from'] = 'מאת:';
		$this->tapuz_labels_name['to-address'] = 'כתובת למשלוח:';
		$this->tapuz_labels_name['from-address'] = 'כתובת איסוף:';
		$this->tapuz_labels_name['to'] = 'אל:';
		$this->tapuz_labels_name['packages'] = 'חבילות:';
		$this->tapuz_labels_name['double'] = 'כפולה:';
		$this->tapuz_labels_name['tel'] = 'טל:';
		$this->tapuz_labels_name['yes'] = 'כן';
		$this->tapuz_labels_name['no'] = 'לא';
		$this->tapuz_labels_name['no'] = 'לא';
		$this->tapuz_labels_name['motor'] = 'דיוור:';
		$this->tapuz_labels_name['bike'] = 'אופנוע';
		$this->tapuz_labels_name['car'] = 'רכב';
		$this->tapuz_labels_name['collect'] = 'גוביינא:';
		$this->tapuz_labels_name['no'] = 'לא';
		$this->tapuz_labels_name['currency'] = 'ש"ח';
		$this->tapuz_labels_name['comments'] = 'הערות למשלוח:';
		$this->ship_data = $ship_data[0];

	}

	/**
	 * Set PDF info
	 */
	private function set_pdf_info() {
		$this->pdf->SetCreator( 'HATAMMY Plugin' );
		$this->pdf->SetAuthor( 'HATAMMY Plugin' );
		$this->pdf->SetTitle( 'Tapuz label' );
		$this->pdf->SetSubject( 'Tapuz shipping label' );
		$this->pdf->setPrintHeader( false );
		$this->pdf->setPrintFooter( false );
		$this->pdf->SetAutoPageBreak( false );
		$this->pdf->SetTextColor( 0, 0, 0 );
		$this->pdf->setRTL(true);
	}

	/**
	 * Create DYMO Label
	 */
	public function create_dymo_label (){
		$this->pdf = new TCPDF( 'L', 'mm', array( 54, 101 ), true, 'UTF-8', false );
		$this->set_pdf_info();
		$this->pdf->SetFont( 'freeserif', '', 12 );
		$this->pdf->AddPage();

		//barcode
		$this->pdf->SetY( 3 );
		$this->pdf->write1DBarcode( $this->ship_data['delivery_number'], 'C39', '', '', '', 20, 0.7, $this->barcode_style, 'N' );

		//from name title
		$this->pdf->SetY( 25 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 10, 5, $this->tapuz_labels_name['from'] );

		//from name
		$this->pdf->SetY( 25 );
		$this->pdf->SetX( 13 );
		$this->pdf->Cell( 80, 5, $this->ship_data['collect_company'], 1 );

		//Packges title
		$this->pdf->SetY( 32 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 15, 5, $this->tapuz_labels_name['packages'] );

		//Packges
		$this->pdf->SetY( 32 );
		$this->pdf->SetX( 18 );
		$this->pdf->Cell( 7, 5, $this->ship_data['packages'] , 1 );

		//return title
		$this->pdf->SetY( 32 );
		$this->pdf->SetX( 26 );
		$this->pdf->Cell( 14, 5, $this->tapuz_labels_name['double']);

		//Return
		$this->pdf->SetY( 32 );
		$this->pdf->SetX( 41 );
		if ($this->ship_data['return'] == '2'){
			$this->pdf->Cell( 7, 5, $this->tapuz_labels_name['yes'] , 1 );
		} else {
			$this->pdf->Cell( 7, 5, $this->tapuz_labels_name['no'] , 1 );
		}

		//Motor title
		$this->pdf->SetY( 32 );
		$this->pdf->SetX( 49 );
		$this->pdf->Cell( 12, 5, $this->tapuz_labels_name['motor'] );

		//Motor
		$this->pdf->SetY( 32 );
		$this->pdf->SetX( 62 );
		if ($this->ship_data['motor'] == '1'){
			$this->pdf->Cell( 16, 5, $this->tapuz_labels_name['bike'] , 1 );
		} else {
			$this->pdf->Cell( 16, 5, $this->tapuz_labels_name['car'] , 1 );
		}

		//To name title
		$this->pdf->SetY( 39 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 8, 5, $this->tapuz_labels_name['to'] );

		//To name
		$this->pdf->SetY( 39 );
		$this->pdf->SetX( 11 );
		$this->pdf->Cell( 40, 5, $this->ship_data['contact_name'], 1 );

		//Phone title
		$this->pdf->SetY( 39 );
		$this->pdf->SetX( 52 );
		$this->pdf->Cell( 8, 5, $this->tapuz_labels_name['tel'] );

		//Phone
		$this->pdf->SetY( 39 );
		$this->pdf->SetX( 61 );
		$this->pdf->Cell( 30, 5, $this->ship_data['contact_phone'], 1 );

		//To address title
		$this->pdf->SetY( 46 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 27, 5, $this->tapuz_labels_name['to-address'] );

		//To address
		$this->pdf->SetY( 46 );
		$this->pdf->SetX( 30 );
		$this->pdf->Cell( 70, 5, $this->ship_data['street']. ', ' .$this->ship_data['city'] , 1 );

		$this->pdf->Output( 'tapuz_label_'.$this->ship_data['delivery_number'].'.pdf', 'I', true );

	}

	public function create_a4_label (){
		$this->pdf = new TCPDF( 'L', 'mm', array( 139.7, 210 ), true, 'UTF-8', false );
		$this->set_pdf_info();
		$this->pdf->SetFont( 'freeserif', '', 16 );
		$this->pdf->AddPage();

		//barcode
		$this->pdf->SetY( 3 );
		$this->pdf->write1DBarcode( $this->ship_data['delivery_number'], 'C39', '', '', '', 30, 0.7, $this->barcode_style, 'N' );

		//from name title
		$this->pdf->SetY( 35);
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 10, 5, $this->tapuz_labels_name['from'] );

		//from name
		$this->pdf->SetY( 35 );
		$this->pdf->SetX( 15 );
		$this->pdf->Cell( 100, 5, $this->ship_data['collect_company'], 1 );

		//from address title
		$this->pdf->SetY( 45 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 24, 5, $this->tapuz_labels_name['from-address'] );

		//from address
		$this->pdf->SetY( 45 );
		$this->pdf->SetX( 35 );
		$this->pdf->Cell( 120, 5, $this->ship_data['collect_street'].' '.$this->ship_data['collect_street_number'].', ' .$this->ship_data['collect_city'] , 1 );

		//Package title
		$this->pdf->SetY( 55 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 15, 5, $this->tapuz_labels_name['packages'] );

		//Packges
		$this->pdf->SetY( 55 );
		$this->pdf->SetX( 22 );
		$this->pdf->Cell( 9, 5, $this->ship_data['packages'] , 1 );

		//return title
		$this->pdf->SetY( 55 );
		$this->pdf->SetX( 33);
		$this->pdf->Cell( 14, 5, $this->tapuz_labels_name['double']);

		//Return
		$this->pdf->SetY( 55 );
		$this->pdf->SetX( 51 );
		if ($this->ship_data['return'] == '2'){
			$this->pdf->Cell( 9, 5, $this->tapuz_labels_name['yes'] , 1 );
		} else {
			$this->pdf->Cell( 9, 5, $this->tapuz_labels_name['no'] , 1 );
		}

		//Motor title
		$this->pdf->SetY( 55 );
		$this->pdf->SetX( 60);
		$this->pdf->Cell( 12, 5, $this->tapuz_labels_name['motor'] );

		//Motor
		$this->pdf->SetY( 55 );
		$this->pdf->SetX( 76 );
		if ($this->ship_data['motor'] == '1'){
			$this->pdf->Cell( 18, 5, $this->tapuz_labels_name['bike'] , 1 );
		} else {
			$this->pdf->Cell( 18, 5, $this->tapuz_labels_name['car'] , 1 );
		}

		//collect title
		$this->pdf->SetY( 55 );
		$this->pdf->SetX( 97 );
		$this->pdf->Cell( 12, 5, $this->tapuz_labels_name['collect'] );

		//collect
		$this->pdf->SetY( 55 );
		$this->pdf->SetX( 117 );
		if (empty ($this->ship_data['collect'])){
			$this->pdf->Cell( 18, 5, $this->tapuz_labels_name['no'] , 1 );
		} else {
			$this->pdf->Cell( 18, 5, $this->ship_data['collect'] , 1 );
			//currency
			$this->pdf->SetY( 55 );
			$this->pdf->SetX( 135 );
			$this->pdf->Cell( 12, 5, $this->tapuz_labels_name['currency'] );
		}

		//To name title
		$this->pdf->SetY( 65 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 10, 5, $this->tapuz_labels_name['to'] );

		//To name
		$this->pdf->SetY( 65 );
		$this->pdf->SetX( 11 );
		$this->pdf->Cell( 70, 5, $this->ship_data['contact_name'], 1 );

		//Phone title
		$this->pdf->SetY( 65);
		$this->pdf->SetX( 83 );
		$this->pdf->Cell( 8, 5, $this->tapuz_labels_name['tel'] );

		//Phone
		$this->pdf->SetY( 65 );
		$this->pdf->SetX( 92 );
		$this->pdf->Cell( 50, 5, $this->ship_data['contact_phone'], 1 );

		//To address title
		$this->pdf->SetY( 75 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 27, 5, $this->tapuz_labels_name['to-address'] );

		//To address
		$this->pdf->SetY( 75 );
		$this->pdf->SetX( 38 );
		$this->pdf->Cell( 165, 5, $this->ship_data['street']. ', ' .$this->ship_data['city'] , 1 );

		//Comments title
		$this->pdf->SetY( 85 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 27, 5, $this->tapuz_labels_name['comments'] );

		//Comments
		$this->pdf->SetY( 85 );
		$this->pdf->SetX( 38 );
		$this->pdf->Cell( 165, 5, $this->ship_data['street']. ', ' .$this->ship_data['city'] , 1 );

		$this->pdf->Output( 'tapuz_label_'.$this->ship_data['delivery_number'].'.pdf', 'I', true );

	}

	public function create_a4_label_logo ($tapuz_settings_logo){
		$this->pdf = new TCPDF( 'L', 'mm', array( 139.7, 210 ), true, 'UTF-8', false );
		$this->set_pdf_info();
		$this->pdf->SetFont( 'freeserif', '', 16 );
		$this->pdf->AddPage();

		// set JPEG quality
		$this->pdf->setJPEGQuality(75);

		// Image method signature:
		// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
		$this->pdf->Image($tapuz_settings_logo, 'C', 3, "", 35, '', '', 'C', true, 150, 'C', false, false, 0, false, false, false);
		
		//barcode
		$this->pdf->SetY( 40 );
		$this->pdf->write1DBarcode( $this->ship_data['delivery_number'], 'C39', '', '', '', 30, 0.7, $this->barcode_style, 'N' );

		//from name title
		$this->pdf->SetY( 72 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 10, 5, $this->tapuz_labels_name['from'] );

		//from name
		$this->pdf->SetY( 72 );
		$this->pdf->SetX( 15 );
		$this->pdf->Cell( 100, 5, $this->ship_data['collect_company'], 1 );

		//from address title
		$this->pdf->SetY( 82 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 24, 5, $this->tapuz_labels_name['from-address'] );

		//from address
		$this->pdf->SetY( 82 );
		$this->pdf->SetX( 35 );
		$this->pdf->Cell( 120, 5, $this->ship_data['collect_street'].' '.$this->ship_data['collect_street_number'].', ' .$this->ship_data['collect_city'] , 1 );

		//Package title
		$this->pdf->SetY( 92 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 15, 5, $this->tapuz_labels_name['packages'] );

		//Packges
		$this->pdf->SetY( 92 );
		$this->pdf->SetX( 22 );
		$this->pdf->Cell( 9, 5, $this->ship_data['packages'] , 1 );

		//return title
		$this->pdf->SetY( 92 );
		$this->pdf->SetX( 33);
		$this->pdf->Cell( 14, 5, $this->tapuz_labels_name['double']);

		//Return
		$this->pdf->SetY( 92 );
		$this->pdf->SetX( 51 );
		if ($this->ship_data['return'] == '2'){
			$this->pdf->Cell( 9, 5, $this->tapuz_labels_name['yes'] , 1 );
		} else {
			$this->pdf->Cell( 9, 5, $this->tapuz_labels_name['no'] , 1 );
		}

		//Motor title
		$this->pdf->SetY( 92 );
		$this->pdf->SetX( 60);
		$this->pdf->Cell( 12, 5, $this->tapuz_labels_name['motor'] );

		//Motor
		$this->pdf->SetY( 92 );
		$this->pdf->SetX( 76 );
		if ($this->ship_data['motor'] == '1'){
			$this->pdf->Cell( 18, 5, $this->tapuz_labels_name['bike'] , 1 );
		} else {
			$this->pdf->Cell( 18, 5, $this->tapuz_labels_name['car'] , 1 );
		}

		//collect title
		$this->pdf->SetY( 92 );
		$this->pdf->SetX( 97 );
		$this->pdf->Cell( 12, 5, $this->tapuz_labels_name['collect'] );

		//collect
		$this->pdf->SetY( 92 );
		$this->pdf->SetX( 117 );
		if (empty ($this->ship_data['collect'])){
			$this->pdf->Cell( 18, 5, $this->tapuz_labels_name['no'] , 1 );
		} else {
			$this->pdf->Cell( 18, 5, $this->ship_data['collect'] , 1 );
			//currency
			$this->pdf->SetY( 92 );
			$this->pdf->SetX( 135 );
			$this->pdf->Cell( 12, 5, $this->tapuz_labels_name['currency'] );
		}

		//To name title
		$this->pdf->SetY( 102 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 10, 5, $this->tapuz_labels_name['to'] );

		//To name
		$this->pdf->SetY( 102 );
		$this->pdf->SetX( 11 );
		$this->pdf->Cell( 70, 5, $this->ship_data['contact_name'], 1 );

		//Phone title
		$this->pdf->SetY( 102 );
		$this->pdf->SetX( 83 );
		$this->pdf->Cell( 8, 5, $this->tapuz_labels_name['tel'] );

		//Phone
		$this->pdf->SetY( 102 );
		$this->pdf->SetX( 92 );
		$this->pdf->Cell( 50, 5, $this->ship_data['contact_phone'], 1 );

		//To address title
		$this->pdf->SetY( 112 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 27, 5, $this->tapuz_labels_name['to-address'] );

		//To address
		$this->pdf->SetY( 112 );
		$this->pdf->SetX( 38 );
		$this->pdf->Cell( 165, 5, $this->ship_data['street']. ', ' .$this->ship_data['city'] , 1 );

		//Comments title
		$this->pdf->SetY( 122 );
		$this->pdf->SetX( 2 );
		$this->pdf->Cell( 27, 5, $this->tapuz_labels_name['comments'] );

		//Comments
		$this->pdf->SetY( 122 );
		$this->pdf->SetX( 38 );
		$this->pdf->Cell( 165, 5, $this->ship_data['street']. ', ' .$this->ship_data['city'] , 1 );

		$this->pdf->Output( 'tapuz_label_'.$this->ship_data['delivery_number'].'.pdf', 'I', true );

	}
}
