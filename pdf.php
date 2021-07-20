<?php
require_once('system/library/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
		
        $this->SetFont('times', '', 12);
       
        $this->Write(10, 'http://mouthpiececomparator.com', 'http://mouthpiececomparator.com', false, 'C', true);

    }
}

if (isset($_POST['data'])) {

// create new PDF document
$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);


// set default header data
$pdf->SetHeaderData('moutpiece.png', 50, '', '');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

$pdf->AddPage();

$data = json_decode(base64_decode($_POST['data']));

$name = base64_decode($_POST['name']);

$series = "All";

if (!empty($_POST['series']) && $_POST['series'] != '0') {
$series = $_POST['series'];
}

$pdf->SetFont('times', 'BI', 20);

$pdf->SetTitle($name);

// set some text to print
$txt = <<<EOD
$name
Series: $series


EOD;

// print a block of text using Write()
$pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);

$pdf->SetFont('times', '', 12);

// -----------------------------------------------------------------------------

$tbl = '
<table border="0.2" cellpadding="5">

 <tr>
  <td align="center" width="15%" rowspan="2"><b>Model<br>Suffix</b></td>
  <td align="center" width="10%" rowspan="2"><b>Bore</b></td>
  <td align="center" width="20%" colspan="2"><b>Cup Diameter</b></td>
  <td align="center" width="15%" rowspan="2"><b>Cup<br>Depth</b></td>
  <td width="40%" rowspan="2"><b>Description</b></td>
 </tr>
 <tr>
  <td align="center">Inside</td>
  <td align="center">Outside</td>
 </tr>
';


foreach($data as $row) {

if ($row->desc == '') {
$row->desc = 'N/A';
}

$tbl .= "<tr>
  <td align=\"center\">$row->model</td>
  <td align=\"center\">$row->bore</td>
  <td align=\"center\">$row->idiam</td>
  <td align=\"center\">$row->odiam</td>
  <td align=\"center\">$row->depth</td>
  <td>$row->desc</td>
 </tr>";
}

$tbl .= '</table>';

$pdf->writeHTML($tbl, true, false, false, false, '');

if (!empty($_POST['print']) && $_POST['print'] == 1) {
// force print dialog
$js = 'print(true);';

// set javascript
$pdf->IncludeJS($js);
$pdf->Output($name.'.pdf', 'I');
} else {
$pdf->Output($name.'.pdf', 'D');
}
}

?>