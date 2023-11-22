<?php
/**
 * Created by PhpStorm.
 * User: Karan
 * Date: 27/6/16
 * Time: 11:37 AM
 */

class custom_tcpdf extends TCPDF {
    /**
     * Overwrite Header() method.
     * @public
     */
    /*public function Header() {
        // if ($this->tocpage) or
        if ($this->page == 1) {
            // *** replace the following parent::Header() with your code for TOC/page you want page
            // parent::Header();
            // this will add logo and text to first page
            $this->Image(, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $this->SetFont('helvetica', 'B', 14);
            $this->Cell(0, 15, 'First page header text', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        } else {
            // *** replace the following parent::Header() with your code for other pages
            //parent::Header();
            // following will add your own logo ant text to other pages
            $this->Image('http://localhost/other_pages_logo.png', 10, 10, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            $this->SetFont('helvetica', 'B', 14);
            $this->Cell(0, 15, 'Other pages header text', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        }
    }*/
}