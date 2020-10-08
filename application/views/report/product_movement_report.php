<?php
date_default_timezone_set("Asia/Manila");
class PDFREPORT extends TCPDF {

    var $htmlHeader;
    var $htmlHeaderAddress;
    var $periodDate;

    public function setHtmlHeader($htmlHeader) {
        $this->htmlHeader = $htmlHeader;
    }

    public function setHtmlHeaderAddress($htmlHeaderAddress) {
        $this->htmlHeaderAddress = $htmlHeaderAddress;
    }

    public function setPeriodDate($periodDate) {
        $this->htmlPeriodDate = $periodDate;
    }

    public function Header() {
    	$this->setFont("Helvetica", 'B', 15);
        $this->writeHTMLCell(0, 0, 0, 4,
            'RIBSHACK GRILL', 0, 1, 0, '', 'C');
    	$this->setFont("Helvetica", '', 10);
        $this->writeHTMLCell(0, 0, 0, 10,
            'OPERATED BY: '.$this->htmlHeader, 0, 1, 0, '', 'C');
        $this->writeHTMLCell(0, 0, 0, 15,
            $this->htmlHeaderAddress, 0, 1, 0, '', 'C');
		//$currentdate = date("F d, Y h:iA");
		$this->writeHTML("<hr>", true, false, false, false, '');
		$this->writeHTMLCell(0, 0, 6.5, 25, "PRODUCT MOVEMENT SUMMARY", 0, 1, 0, '', 'L');
		$this->writeHTMLCell(0, 0, 0, 25, $this->htmlPeriodDate, 0, 1, 0, '', 'R');
    }

    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 7);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().' / '.$this->getAliasNbPages(), 0, false, 'L', 0, '', 0, false, 'T', 'M');
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, date("F d, Y h:iA"),
        			 0, false, 'R', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 17, 'Printed By: '.$_SESSION["rgc_firstname"]." ".$_SESSION["rgc_lastname"],
        			 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}
// create new PDF document
$pdf = new PDFREPORT("P", 'mm', 'LETTER', true, 'UTF-8', false);

// set auto page breaks
$pdf->SetAutoPageBreak(FALSE, PDF_MARGIN_BOTTOM);
$pdf->SetTitle('RIBSHACK PRODUCT MOVEMENT');
// set default header data
$pdf->setHtmlHeader($operated_by);
$pdf->setHtmlHeaderAddress($address);
$pdf->setPeriodDate($period_date);



$complex_cell_border = array(
   'T' => array('width' => 0),
   'R' => array('width' => 0, 'color' => array(0,0,0), 'dash' => 0),
   'B' => array('width' => 0, 'color' => array(0,0,0), 'dash' => 0),
   'L' => array('width' => 0, 'color' => array(0,0,0), 'dash' => 0),
);

$pdf->setCellPaddings(0.5,1.5,0,0);
$cellheight = 38.5;
$dcount = 0;
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 7);
$pdf->writeHTMLCell(17, 4, 6.5, 34, "Code", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(45, 4, 23.5, 34, "Description", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(10, 4, 68.5, 34, "UoM", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(15, 4, 78.5, 34, "POS", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(14.5, 4, 93.5, 34, "Beg", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(14, 4, 108, 34, "Delivery", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(15, 4, 122, 34, "Trans IN", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(14, 4, 137, 34, "Ending", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(15, 4, 151, 34, "Return", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(15, 4, 166, 34, "Trans OUT", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(12, 4, 181, 34, "Actual", 1, 1, 0, 'top', '');
$pdf->writeHTMLCell(15, 4, 193, 34, "Short/Over", 1, 1, 0, 'top', '');

foreach($report_data as $ind => $row){
   if(count($row) > 1){


       if($dcount > 35){
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 7);
            $pdf->writeHTMLCell(17, 4, 6.5, 34, "Code", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(45, 4, 23.5, 34, "Description", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(10, 4, 68.5, 34, "UoM", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(15, 4, 78.5, 34, "POS", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(14.5, 4, 93.5, 34, "Beg", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(14, 4, 108, 34, "Delivery", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(15, 4, 122, 34, "Trans IN", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(14, 4, 137, 34, "Ending", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(15, 4, 151, 34, "Return", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(15, 4, 166, 34, "Trans OUT", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(12, 4, 181, 34, "Actual", 1, 1, 0, 'top', '');
            $pdf->writeHTMLCell(15, 4, 193, 34, "Short/Over", 1, 1, 0, 'top', '');
            $dcount = 0;
            $cellheight = 38.5;
        }

    	$pdf->SetFont('helvetica', 'B', 7);
    	$pdf->writeHTMLCell(17, 6, 6.5, $cellheight, $row["product_id"], $complex_cell_border, 1, 0, 'T', '');
    	$pdf->writeHTMLCell(45, 6, 23.5, $cellheight, $row["description"], $complex_cell_border, 1, 0, 'T', '');
    	$pdf->writeHTMLCell(10, 6, 68.5, $cellheight, $row["uom_abbr"], $complex_cell_border, 1, 0, 'T', 'C');

    	$pdf->SetFont('helvetica', '', 7);
    	$pdf->writeHTMLCell(15, 6, 78.5, $cellheight, $row["pos_total"], $complex_cell_border, 1, 0, 'T', 'C');
    	$pdf->writeHTMLCell(14.5, 6, 93.5, $cellheight, $row["beginning"], $complex_cell_border, 1, 0, 'T', 'C');
    	$pdf->writeHTMLCell(14, 6, 108, $cellheight, $row["delivery"], $complex_cell_border, 1, 0, 'T', 'C');
    	$pdf->writeHTMLCell(15, 6, 122, $cellheight, $row["trans_in"], $complex_cell_border, 1, 0, 'T', 'C');
    	$pdf->writeHTMLCell(14, 6, 137, $cellheight, $row["ending"], $complex_cell_border, 1, 0, 'T', 'C');
    	$pdf->writeHTMLCell(15, 6, 151, $cellheight, $row["return_stock"], $complex_cell_border, 1, 0, 'T', 'C');
    	$pdf->writeHTMLCell(15, 6, 166, $cellheight, $row["trans_out"], $complex_cell_border, 1, 0, 'T', 'C');
    	$pdf->writeHTMLCell(12, 6, 181, $cellheight, $row["actual"], $complex_cell_border, 1, 0, 'T', 'C');
        if($row["discrepancy"] < 0)
            $pdf->SetTextColor(246,90,55);
    	$pdf->writeHTMLCell(15, 6, 193, $cellheight, $row["discrepancy"], $complex_cell_border, 1, 0, 'T', 'C');
        $pdf->SetTextColor(0,0,0);

    	foreach($row["child"] as $chind => $chrow){
            $cellheight+=6;
            $dcount++;

            if($dcount > 35){
                $pdf->AddPage();
                $pdf->SetFont('helvetica', '', 7);
                $pdf->writeHTMLCell(17, 4, 6.5, 34, "Code", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(45, 4, 23.5, 34, "Description", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(10, 4, 68.5, 34, "UoM", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(15, 4, 78.5, 34, "POS", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(14.5, 4, 93.5, 34, "Beg", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(14, 4, 108, 34, "Delivery", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(15, 4, 122, 34, "Trans IN", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(14, 4, 137, 34, "Ending", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(15, 4, 151, 34, "Return", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(15, 4, 166, 34, "Trans OUT", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(12, 4, 181, 34, "Actual", 1, 1, 0, 'top', '');
                $pdf->writeHTMLCell(15, 4, 193, 34, "Short/Over", 1, 1, 0, 'top', '');
                $dcount = 0;
                $cellheight = 38.5;
            }
    		$pdf->writeHTMLCell(17, 6, 6.5, $cellheight, $chrow["product_id"], $complex_cell_border, 1, 0, 'T', '');
    		$pdf->writeHTMLCell(45, 6, 23.5, $cellheight, $chrow["description"], $complex_cell_border, 1, 0, 'T', '');
    		$pdf->writeHTMLCell(10, 6, 68.5, $cellheight, $chrow["uom"], $complex_cell_border, 1, 0, 'T', 'C');
    		$pdf->writeHTMLCell(15, 6, 78.5, $cellheight, $chrow["pos_total"], $complex_cell_border, 1, 0, 'T', 'C');
    		$pdf->writeHTMLCell(114.5, 6, 93.5, $cellheight, "", $complex_cell_border, 1, 0, 'T', '');

    	}
        
        $cellheight+=6;
        $dcount++;

    }//if $row > 0
}

$pdf->Output();
// $pdf->Write(5, 'Some sample text');

?>
