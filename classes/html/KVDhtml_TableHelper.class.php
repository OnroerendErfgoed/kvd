<?php
/**
 * @package KVD.html
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDhtml_TableHelper 
 * 
 * Maak een HTML tabel aan op basis van enkele parameters.
 * Vooral bedoeld voor datatabellen die een eenvoudige layout hebben, momenteel kunnen er bijvoorbeeld geen cellen samengevoegd worden.
 * @since 2005
 * @package KVD.html
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDhtml_TableHelper {
    /**
     * Array die de headers bevat 
     * @var array
     */
    private $headers;

    /**
     * Array dat de rijen bevat
     * @var array
     */
    private $rows;

    /**
     * Een string die wordt toegevoegd als tfoot element aan de tabel
     * @var string
     */
    private $footer;
    
    /**
     * Het caption element van de tabel. Dit is een apart html element dat buiten de tabel zelf staat.
     * @var string
     */
    private $caption;

    /**
     * De titel van de tabel.
     * @var string
     */
    private $title = null;
    
    /**
     * Het summary element van de tabel
     * @var string
     */
    private $summary;
    
    /**
     * Het aantal kolommen van de tabel
     * @var integer
     */
    protected $numCols;
    
    /**
     * Het aantal rijen van de tabel
     * @var integer
     */
    protected $numRows;
    
    /**
     * Css class-namen die worden toegevoegd aan bepaalde elementen.
     * @var array
     */
    protected $cssClasses;
    
    /**
     * Moet er een afwisselende stijl toegepast worden op de rijen?
     * @var boolean
     */
    protected $alternateRow;
    
    /**
     * Gaat het om een lijst (true) of een record (false)?
     * @var boolean
     */
    protected $lijst;

    /**
     * Waarde die aan een lege cell gegeven wordt (belangrijk voor IE)
     * @var string
     */
    protected $emptyCell;
    
    /**
     * Maak een nieuwe KVDhtml_TableHelper aan.
     */
    public function __construct() {
        $this->clearHeaders();
        $this->clearRows();
        $this->initClasses();
        $this->setAlternateRow();
        $this->setLijst();
        $this->setEmptyCell();
    }

    /**
     * Moeten er afwisselende stijlen toegepast worden op de rijen om een tabel leesbaarder te maken?
     * @param boolean $alternateRow
     */
    public function setAlternateRow ( $alternateRow = true )
    {
        $this->alternateRow = $alternateRow;
    }

    /**
     * Gaat het om een lijst van records (waarde true) of om de weergave van één record (waarde false).
     *
     * Wanneer het om een lijst gaat staan de veldnamen bovenaan, anders staan de veldnamen links.
     * @param boolean $lijst 
     */
    public function setLijst($lijst=true)
    {
        $this->lijst=$lijst;
    }

    /**
     * Stel de waarde in die aan lege cellen gegeven wordt. Belangrijk omdat IE een css-probleem heeft.
     *
     * Wordt standaard ingesteld op een non-breaking space.
     * @param string $value 
     */
    public function setEmptyCell($value='&nbsp;')
    {
        $this->emptyCell = $value;
    }

    /**
     * @param string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * Voeg dit toe als een titel aan de tabel ( komt neer op een rij in Thead die de breedte van de volledig tabel omvat en een aparte css kan hebben).
     * @param string $title
     */
    public function setTableTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    public function clearHeaders()
    {
        $this->headers = array();
    }

    /**
     * Stel een array van kolom- of rijkoppen in.
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->clearHeaders();
        foreach ($headers as $header) {
            $this->addHeader($header);
        }
    }

    /**
     * Voeg 1 kolom- of rijkop toe aan de bestaande koppen.
     * @param string $header
     */
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    /**
     * @param string $footer
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;    
    }

    /**
     * Verwijder de bestaande rijen.
     */
    public function clearRows() 
    {
        $this->rows = array();
    }

    /**
     * Stel meerdere rijen in.
     *
     * Indien er reeds rijen aanwezig zijn, worden deze gewist.
     * @param array $rows Array van arrays.
     * @throws Exception - Als $rows geen array is.
     */
    public function setRows(&$rows)
    {
        $this->clearRows();
        $this->addRows($rows);
    }

    /**
     * Voeg één enkele rij toe aan de tabel.
     *
     * Indien $row geen array is wordt er van uitgegaan dat er maar 1 veld in de rij aanwezig is.
     * @param array $row
     */
    public function addRow(&$row)
    {
        if (!is_array($row)) {
            $row = array ($row);
        }
        $this->rows[] = $row;
    }

    /**
     * Voegt rijen toe aan de reeds bestaande rijen in de tabel.
     * @param array $rows Array van arrays.
     * @throws InvalidArgumentException - Indien $rows geen array is.
     */
    public function addRows(&$rows) {
        if (!is_array($rows)) {
            throw new InvalidArgumentException ( "Ongeldige parameter! $rows is geen array!" );    
        }
        foreach ($rows as &$row) {
            $this->addRow($row);
        }
    }

    /**
     * Stel waarden in voor css classes.
     *
     * De parameter is een array met een sleutel voor elk element waaraan een css-class gekoppeld kan worden.
     * Toegestane waarden zijn Table, THead, TBody, TFoot, TH, TD, TTitel.
     * @param array $classes
     */
    public function setCssClasses(&$classes) {
        foreach ($classes as $location => $classname) {
            $this->cssClasses[$location] = " class=\"$classname\"";
        }
    }

    /**
     * Stel de mogelijk css classes in.
     */
    private function initClasses() {
        $this->cssClasses = array ('Table' => '', 'THead' => '', 'TBody' => '', 'TFoot' => '', 'TH' => '', 'TD' => '', 'TTitel' => '');
    }

    /**
     * Controleer de afmetingen (aantal rijen en kolommen) van de rows array. Nodig om het aantal td elementen te berekenen.
     */
    private function initDimensions()
    {
        $this->numRows = count($this->rows);

        if ( $this->numRows > 0 ) {
            foreach ($this->rows as $row) {
                if ( count($row) > $this->numCols) {
                    $this->numCols = count($row);
                }    
            }
        }
    }

    /**
     * Controleer of er voor elke rij of kolom ook effectief een header aanwezig is.
     *
     * Indien er minder headers dan rijen of kolommen zijn, worden er legen headers toegevoegd om een geldige html-tabel te bekomen.
     * @return integer
     */
    private function headersTeKort()
    {
        $headersNodig = ($this->lijst) ? $this->numCols : $this->numRows;
        $headersTeKort = $headersNodig - count($this->headers);
        if ($headersTeKort > 0) {
            for ($i=0;$i<$headersTeKort;$i++) {
                $this->headers[]= $this->emptyCell;
            }
        }
        return $headersTeKort;
    }

    /**
     * Genereer een Html voorstelling van de header (caption, summary en thead) van een tabel.
     * @return string 
     */
    protected function toHtmlHeader() {
        if (isset($this->summary)) {
            $summary = "summary=\"{$this->summary}\"";
        } else {
            $summary = "";
        }
        $header = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"{$this->cssClasses['Table']} $summary>\n";
        if (isset($this->caption)) {
            $header .= " <caption>{$this->caption}</caption>\n";
        }
        if (isset( $this->title ) || ( isset($this->headers) && $this->lijst)) {
            $header .= " <thead{$this->cssClasses['THead']}>\n";
            if ( isset( $this->title ) ) {
                $colspan = $this->lijst ? $this->numCols : $this->numCols + 1;
                $header .="  <tr>\n   <th{$this->cssClasses['TTitel']} colspan=\"$colspan\">{$this->title}</th>\n  </tr>";
            }
            $header .= "  <tr>\n";
            if ( $this->lijst ) {
                foreach ($this->headers as $colheader) {
                    $header .= "   <th{$this->cssClasses['TH']} scope=\"col\">$colheader</th>\n";
                }
            }
            $header .= "  </tr>\n </thead>\n";
        }
        return $header;
    }

    /**
     * Genereer een Html voorstelling van het lichaam van de tabel (tbody).
     *
     * Indien een rij minder cellen bevat dan er headers zijn, wordt deze uitgevuld met lege cellen.
     * @return string 
     */
    protected function toHtmlBody() {
        if (!isset($this->rows)) {
            return '';
        }
        $body = " <tbody{$this->cssClasses['TBody']}>\n";
        $rowCounter = 1;
        foreach ($this->rows as &$row) {
            if ($this->alternateRow) {
                if ($rowCounter % 2 == 0) {
                    $rowclass='class="even"';
                } else {
                    $rowclass='class="oneven"';
                }
            } 
            $body .= "  <tr $rowclass>\n";
            if (!$this->lijst) {
                $body .= "   <th{$this->cssClasses['TH']}>{$this->headers[$rowCounter-1]}</th>\n";
            }
            foreach ($row as $cell) {
                $body .= "   <td{$this->cssClasses['TD']}>$cell</td>\n";
            }
            if (count($row) != $this->numCols) {
                $teKort = $this->numCols - count($row);
                for ($i=0; $i < $teKort; $i++) {
                    $body .= "   <td{$this->cssClasses['TD']}>{$this->emptyCell}</td>\n";
                }
            }
            $body .= "  </tr>\n";
            $rowCounter++;
        }
        $body .= " </tbody>\n";
        return $body;
    }

    /**
     * Genereer een Html voorstelling van footer van de tabel (tfoot) en sluit de tabel.
     * 
     * @return string
     */
    protected function toHtmlFooter() {
        $footer = '';
        if ( $this->footer !== null ) {
            $numCols = $this->lijst ? $this->numCols : $this->numCols +1;
            $footer = " <tfoot{$this->cssClasses['TFoot']}>\n  <tr>\n";
            $footer .= "   <td colspan=\"{$numCols}\">{$this->footer}</td>\n";
            $footer .= "  </tr>\n </tfoot>\n";    
        }
        $footer .= "</table>\n";
        return $footer;
    }
    
    /**
     * Genereer een Html voorstelling van de tabel.
     * 
     * @return string
     */
    public function toHtml() {
        $this->initDimensions();
        $this->headersTeKort();
        $html = $this->toHtmlHeader();
        $html .= $this->toHtmlBody();
        $html .= $this->toHtmlFooter();
        return $html;
    }
}
?>
