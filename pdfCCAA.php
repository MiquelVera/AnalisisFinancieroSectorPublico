<?php
session_start();
require_once('includesWeb/daos/DAOConsultor.php');

if(isset($_COOKIE["ccaa"]))
    $nombre = $_COOKIE["ccaa"];


/* RECOGEMOS LOS DATOS DE LA CCAA */
$daoccaa = new DAOConsultor();
$ccaa = $daoccaa->getCCAA(addslashes($nombre));
$ccaaNac = $daoccaa->getCCAA('NACIONAL');


/* AÑADIMOS LA LIBRERÍA TCPDF */

require_once('includes/tcpdf/tcpdf_import.php');

class MYTCPDF extends TCPDF {

	//Page header
	public function Header() {
        $this->setY(15);
		// Set font
		$this->setFont('helvetica', 'B', 20);
		// Title
		$this->Cell(0, 15, 'Informe de comunidad autónoma', 'B', false, 'C', 0, '', 0, false, 'M', 'M');
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->setY(-15);
		// Set font
		$this->setFont('helvetica', 'I', 8);
        //Company name
		$this->Cell(0, 10, 'Noster Economía', 'T', false, 'L', 0, '', 0, false, 'T', 'M');
		// Page number
		$this->Cell(0, 10, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
	}
}

$pdf = new MYTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Noster Economía');
$pdf->setTitle($nombre . ' (CCAA)');

//$pdf->setPrintHeader(true);
//$pdf->setPrintFooter(true);

$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

if(@file_exists(dirname(__FILE__) . '/lang/eng.php')){
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}

$pdf->setFont('helvetica', '', 10);

$pdf->AddPage();


$html = '
    <span style="text-align:justify;">
    <h1>' . $nombre . '</h1>
';

/* DATOS GENERALES*/
$quote1 = "";
$quote2 = "";
$quoteNum = 1;

foreach($ccaa->getScoring() as $clave => $valor){
    $tend = $ccaa->getTendencia();
    $html .='<i><b>Rating ' . $clave . ': ' . $valor . '</b><br>Tendencia: ' . $tend[$clave];

    switch($valor){
        case "A":
            switch($tend[$clave]){
                case "Positiva":
                    $infoRating = "La entidad muestra una elevada fortaleza financiera, lo que supone los niveles comparativos más bajos de riesgo, y una evolución positiva en los últimos años.";
                    break;
                case "Estable":
                    $infoRating = "La entidad muestra una elevada fortaleza financiera, lo que supone los niveles comparativos más bajos de riesgo, y una evolución estable en los últimos años.";
                    break;
                case "Negativa":
                    $infoRating = "La entidad muestra una elevada fortaleza financiera, lo que supone los niveles comparativos más bajos de riesgo, a pesar de la evolución negativa de los últimos años.";
                    break;
            }
            break;
        case "B":
            switch($tend[$clave]){
                case "Positiva":
                    $infoRating = "La entidad muestra una situación financiera holgada, con un nivel relativamente bajo de riesgo, y una evolución positiva en los últimos años.";
                    break;
                case "Estable":
                    $infoRating = "La entidad muestra una situación financiera holgada, con un nivel relativamente bajo de riesgo, que se ha comportado de forma estable durante los últimos años.";
                    break;
                case "Negativa":
                    $infoRating = "La entidad muestra una situación financiera holgada, con un nivel relativamente bajo de riesgo, a pesar del empeoramiento de los últimos años.";
                    break;
            }
            break;
        case "C":
            switch($tend[$clave]){
                case "Positiva":
                    $infoRating = "La entidad muestra una situación financiera media lo que supone un nivel medio-alto de riesgo, a pesar de la evolución positiva de los últimos años.";
                    break;
                case "Estable":
                    $infoRating = "La entidad muestra una situación financiera media lo que supone un nivel medio-alto de riesgo, con un comportamiento similar durante los últimos años.";
                    break;
                case "Negativa":
                    $infoRating = "La entidad muestra una situación financiera media lo que supone un nivel medio-alto de riesgo tras una evolución negativa en los últimos años.";
                    break;
            }
            break;
        case "D":
            switch($tend[$clave]){
                case "Positiva":
                    $infoRating = "La entidad muestra una situación financiera complicada lo que supone un nivel alto de riesgo, a pesar de la evolución positiva de los últimos años.";
                    break;
                case "Estable":
                    $infoRating = "La entidad muestra una situación financiera complicada lo que supone un nivel alto de riesgo, con un comportamiento similar durante los últimos años.";
                    break;
                case "Negativa":
                    $infoRating = "La entidad muestra una situación financiera complicada lo que supone un nivel alto de riesgo, tras una evolución negativa en los últimos años.";
                    break;
            }
            break;
        case "E":
            switch($tend[$clave]){
                case "Positiva":
                    $infoRating = "La entidad muestra una situación financiera muy deteriorada lo que supone un nivel muy elevado de riesgo, a pesar de la la evolución positiva de los últimos años.";
                    break;
                case "Estable":
                    $infoRating = "La entidad muestra una situación financiera muy deteriorada lo que supone un nivel muy elevado de riesgo, con un comportamiento similar durante los últimos años.";
                    break;
                case "Negativa":
                    $infoRating = "La entidad muestra una situación financiera muy deteriorada lo que supone un nivel muy elevado de riesgo, y una evolución negativa en los últimos años.";
                    break;
            }
            break;
    }

    if($quoteNum == 1){
        $quote1 = $infoRating;
    }
    else if ($quoteNum == 2){
        $quote2 = $infoRating;
    }

    $html .= '<sup>'.$quoteNum.'</sup></i><br><br>';
    /*$pdf->setY(-25);
    $pdf->Cell(0, 10, '<sup>'.$quoteNum.'</sup>', 0, false, 'R', 0, '', 0, false, 'T', 'M');
    $pdf->setY(25);*/
    $quoteNum++;

}

$html .= '
    <h3>Información General</h3>
    <br><b>Presidente de la comunidad:  </b>'.$ccaa->getNombrePresidente().' '.$ccaa->getApellido1().' '.$ccaa->getApellido2().'
    <br><b>Vigencia:  </b>'.$ccaa->getVigencia().'
    <br><b>Partido político: </b>'.$ccaa->getPartido().'
    <br><b>CIF:  </b>'.$ccaa->getCif().'
    <br><b>Via:  </b>'.$ccaa->getTipoVia().' '.$ccaa->getNombreVia().' '.$ccaa->getNumVia().'
    <br><b>Teléfono:  </b>'.$ccaa->getTelefono().'
    <br><b>Código Postal:  </b>'.$ccaa->getCodigoPostal().'
';

if($ccaa->getFax() == ''){
    $html .= '<br><b>Fax: </b>N/A ';
}
else{
    $html .= '<br><b>Fax: </b>'.$ccaa->getFax().'';
}

if($ccaa->getWeb() == ''){
    $html .= '<br><b>Sitio web:  </b>N/A';
}
else{
    $html .= '<br><b>Sitio web:  </b><a href="https://'.$ccaa->getWeb().'" target="_blank">'.$ccaa->getWeb().'</a>';
}

if($ccaa->getMail() == ''){
    $html .= '<br><b>Correo electrónico: </b>N/A';
}
else{
    $html .= '<br><b>Correo electrónico: </b>'.$ccaa->getMail();
}


/* DATOS ECONÓMICOS */
$html.= '
    <br><br>
    <h3>Datos económicos</h3><span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">
        <thead>
            <tr>
                <th colspan="2">Población (Año ' . key($ccaa->getPoblacion()).'): ' . number_format(($ccaa->getPoblacion())[key($ccaa->getPoblacion())], 0, "",".") . '</th>
                <th colspan="2">PIB per cápita (Año ' . key($ccaa->getPibc()).'): ' . number_format(($ccaa->getPibc())[key($ccaa->getPibc())]*1000, 0, "",".") . '</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Incremento del PIB de la comunidad
                    <ul>
';

                    foreach($ccaa->getIncrPib() as $clave=>$valor){
                        $html .= '<li>'. $clave . ': ' . ($valor*100) .'%</li>';
                    }
$html .='
                    </ul>
                </td>
                <td>
                    Incremento del PIB nacional
                    <ul>
';

                    foreach($ccaaNac->getIncrPib() as $clave=>$valor){
                        $html .= '<li>' . $clave . ': ' . ($valor*100).'%</li>';
                    }

$html .= '
                    </ul>
                </td>
                <td>
                    Paro
                    <ul>
';

                    foreach($ccaa->getParo() as $array){
                        $html .= '<li>'.$array[0].' (Trimestre '.$array[1].'): '.($array[2]*100).'%</li>';
                    }

$html .= '                  
                    </ul>
                </td>
                <td>
                    Paro nacional
                    <ul>
';
                    foreach($ccaaNac->getParo() as $array){
                        $html .= '<li>'.$array[0].' (Trimestre '.$array[1].'): '.($array[2]*100).'%</li>';
                    }

$html .= '
                    </ul>
                </td>
            </tr>
            <tr>
                <td>
                    Transacciones inmobiliarias
                    <ul>
';
                    foreach($ccaa->getTransacInmobiliarias() as $array){
                        $html .= '<li>'.$array[0].' (Trimestre '.$array[1].'): '.($array[2]*100).'%</li>';
                    }

$html .= '
                    </ul>
                </td>
                <td>
                    Transacciones inmobiliarias nacionales
                    <ul>
';
                    foreach($ccaaNac->getTransacInmobiliarias() as $array){
                        $html .= '<li>'.$array[0].' (Trimestre '.$array[1].'): '.($array[2]*100).'%</li>';
                    }
$html .='
                    </ul>
                </td>
                <td>
                    Crecimiento de las empresas en la comunidad
                    <ul>
';
                    foreach($ccaa->getEmpresas() as $clave=>$valor){
                        $html .= '<li>'.$clave.': '.($valor*100).'%</li>';
                    }
$html .='
                    </ul>
                </td>
                <td>
                    Crecimiento de las empresas a nivel nacional
                    <ul>
';
                    foreach($ccaaNac->getEmpresas() as $clave=>$valor){
                        $html .= '<li>'.$clave.': '.($valor*100).'%</li>';
                    }
$html .='
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
    </span>
';

$html .= '
    </span>
';

$pdf->writeHTML($html, true, false, false, false, 'C');


/* PÁGINA 2 */

$pdf->AddPage();

//Resultados Presupuestarios
$html = '
<span style="text-align:justify;">
<h3><b>Resultado presupuestario y endeudamiento</b></h3>
';
for($i=0;$i<4;$i++){
    if($i==0) $tmp=$ccaa->getCCAAPib();
    else if ($i==1) $tmp=$ccaaNac->getCCAAPib();
    else if ($i==2) $tmp=$ccaa->getDeudaVivaIngrCor();
    else if ($i==3) $tmp=$ccaaNac->getDeudaVivaIngrCor();
    $html .= '<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th></th>';
    if($i<2){
        foreach($tmp as $clave=>$valor){
            $html .= '<th>'.$clave.'</th>';
        }
    }
    else {
        foreach($tmp as $array){
            $html .= '<th>'.$array[0].' (Trimestre '.$array[1].')</th>';
        }
    }
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    $html .= '<tr>';
    if($i==0) $html .= '<th>Resultado presupuestario</th>';
    else if ($i==1) $html .= '<th>Resultado presupuestario nacional</th>';
    else if ($i==2) $html .= '<th>Endeudamiento</th>';
    else if ($i==3) $html .= '<th>Endeudamiento media nacional</th>';
    if($i<2){
        foreach($tmp as $clave=>$valor){
            $html .= '<td>'.($valor*100).'%</td>';
        }
    }
    else {
        foreach($tmp as $array){
            $html .= '<td>'.($array[2]*100).'%</td>';
        }
    }
    $html .= '</tr>';
    $html .= '</tbody>';
    $html .= '</table></span>';
    $html .= '<br>';
}

$html.= '
<h3>Ingresos (en €)</h3>
<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">
    <thead>
        <tr>
            <th></th>
            <th colspan="3">Liquidación de derechos reconocidos</th>
        </tr>
        <tr>
            <th>Ingresos</th>
';
        foreach($ccaa->getImpuestosDirectos1() as $clave=>$valor){
            $html.= '<th>'.$clave.'</th>';
        }

$html.='
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1. Impuestos directos </td>
';
        foreach($ccaa->getImpuestosDirectos1() as $clave=>$valor){
            $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
        }
$html.='
        </tr>
        <tr>
            <td>2. Impuestos indirectos</td>
';
            foreach($ccaa->getImpuestosIndirectos1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <td>3. Tasas, precios, públicos y otros ingresos</td>
';
            foreach($ccaa->getTasasPreciosOtros1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <td>4. Transferencias corrientes</td>
';
            foreach($ccaa->getTransferenciasCorrientes1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <td>5. Ingresos patrimoniales</td>
';
            foreach($ccaa->getIngresosPatrimoniales1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <td>Total ingresos corrientes</td>
';
            foreach($ccaa->getTotalIngresosCorrientes1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <td>6. Enajenación de inversiones reales</td>
';
            foreach($ccaa->getEnajenacionInversionesReales1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <td>7. Transferencias de capital</td>
';
            foreach($ccaa->getTransferenciasCapital1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <th>Ingresos no financieros</th>
';
            foreach($ccaa->getTotalIngresosNoCorrientes1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <td>8. Activos financieros</td>
';
            foreach($ccaa->getActivosFinancieros1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <td>9. Pasivos financieros</td>
';
            foreach($ccaa->getPasivosFinancieros1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
        <tr>
            <td>Ingresos totales</td>
';
            foreach($ccaa->getTotalIngresos1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
$html.='
        </tr>
    </tbody>
</table>
</span>
';


$html.= '</span>';

$pdf->writeHTML($html, true, false, false, false, 'C');


/* PÁGINA 3 */

$pdf->AddPage();

$html = '
<span style="text-align:justify;">
<h3>Gastos (en €)</h3>
<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">
    <thead>
        <tr>
            <th></th>
            <th colspan="3" style="height:40px">Liquidación  obligaciones reconocidos</th>
        </tr>
        <tr>
            <th>Gastos</th>
            ';
            foreach($ccaa->getGastosPersonal1() as $clave=>$valor){
                $html.= '<th>'.$clave.'</th>';
            }
            $html .= '
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1. Gastos del personal</td>
            ';
            foreach($ccaa->getGastosPersonal1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>2. Gastos corrientes en bienes y servicios</td>
            ';
            foreach($ccaa->getGastosCorrientesBienesServicios1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>3. Gastos financieros</td>
            ';
            foreach($ccaa->getGastosFinancieros1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>4. Transferencias corrientes</td>
            ';
            foreach($ccaa->getTransferenciasCorrientesGastos1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>5. Fondo de contingencia</td>
            ';
            foreach($ccaa->getFondoContingencia1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>Total gastos corrientes</td>
            ';
            foreach($ccaa->getTotalGastosCorrientes1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>6. Inversiones reales</td>
            ';
            foreach($ccaa->getInversionesReales1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>7. Transferencias de capital</td>
            ';
            foreach($ccaa->getTransferenciasCapitalGastos1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>Gastos no financieros</td>
            ';
            foreach($ccaa->getTotalGastosNoFinancieros1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>8. Activos financieros</td>
            ';
            foreach($ccaa->getActivosFinancieros1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>9. Pasivos financieros</td>
            ';
            foreach($ccaa->getPasivosFinancierosGastos1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
        <tr>
            <td>Gasto total</td>
            ';
            foreach($ccaa->getTotalGastos1() as $clave=>$valor){
                $html.= '<td>'.number_format($valor, 2, ",",".").'</td>';
            }
            $html .= '
        </tr>
    </tbody>
</table>
</span>
';

$html .= '
<br><br>
<h3>Solvencia</h3>
';
for($i=0;$i<3;$i++){
    if($i==0) $tmp=$ccaa->getRSosteFinanciera();
    else if ($i==1) $tmp=$ccaa->getRRigidez();
    else if ($i==2) $tmp=$ccaa->getRSosteEndeuda();
    $html .='<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">';
    $html .='<thead>';
    $html .='<tr>';
    $html .='<th></th>';
    foreach($tmp as $clave=>$valor){
        $html .='<th>'.$clave.'</th>';
    }
    $html .='</tr>';
    $html .='</thead>';
    $html .='<tbody>';
    $html .='<tr>';
    if($i==0) $html .='<th>Sostenibilidad financiera</th>';
    else if ($i==1) $html .='<th>Apalancamiento</th>';
    else if ($i==2) $html .='<th>Sostenibilidad de la deuda</th>';
    foreach($tmp as $clave=>$valor){
        $html .='<td>'.($valor*100).'%</td>';
    }
    $html .='</tr>';
    $html .='</tbody>';
    $html .='</table></span>';
    $html .='<br>';
}

$html .= '</span>';


$pdf->writeHTML($html, true, false, false, false, 'C');

/* PÁGINA 4 */

$pdf->AddPage();

$html = '<span style="text-align:justify;">
<h3>Liquidez</h3>
';
for($i=0;$i<2;$i++){
    if($i==0) $tmp=$ccaa->getPMP();
    else if ($i==1) $tmp=$ccaaNac->getPMP();
    $html .= '<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th></th>';
    foreach($tmp as $array){
        $html .= '<th>'.$array[0].' (Mes '.$array[1].')</th>';
    }
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    $html .= '<tr>';
    if($i==0) $html .= '<th>Periodo medio de pago</th>';
    else if ($i==1) $html .= '<th>Periodo medio de pago nacional</th>';
    foreach($tmp as $array){
        $html .= '<td>'.$array[2].' días</td>';
    }
    $html .= '</tr>';
    $html .= '</tbody>';
    $html .= '</table></span>';
    $html .= '<br>';
}

$html .='
<br><br>
<h3>Eficiencia</h3>
';

for($i=0;$i<2;$i++){
    if($i==0) $tmp=$ccaa->getREfic();
    else if ($i==1) $tmp=$ccaaNac->getREfic();
    $html .= '<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th></th>';
    foreach($tmp as $clave=>$valor){
        $html .= '<th>'.$clave.'</th>';
    }
    $html .= '</tr>';
    $html .='</thead>';
    $html .= '<tbody>';
    $html .= '<tr>';
    if($i==0) $html .= '<th>Eficiencia</th>';
    else if ($i==1) $html .= '<th>Eficiencia media</th>';
    foreach($tmp as $clave=>$valor){
        $html .= '<td>'.($valor*100).'%</td>';
    }
    $html .= '</tr>';
    $html .='</tbody>';
    $html .= '</table></span>';
    $html .= '<br>';
}

$html .= '
<br><br>
<h3>Gestión presupuestaria</h3>
';
for($i=0;$i<4;$i++){
    if($i==0) $tmp=$ccaa->getREjeIngrCorr();
    else if ($i==1) $tmp=$ccaaNac->getREjeIngrCorr();
    else if ($i==2) $tmp=$ccaa->getREjeGastosCorr();
    else if ($i==3) $tmp=$ccaaNac->getREjeGastosCorr();
    $html .= '<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th></th>';
    foreach($tmp as $clave=>$valor){
        $html .= '<th>'.$clave.'</th>';
    }
    $html .= '</tr>';
    $html .='</thead>';
    $html .= '<tbody>';
    $html .= '<tr>';
    if($i==0) $html .= '<th>Ejecución sobre ingresos corrientes</th>';
    else if ($i==1) $html .= '<th>Ejecución media sobre ingresos corrientes</th>';
    else if ($i==2) $html .= '<th>Ejecución sobre gastos corrientes</th>';
    else if ($i==3) $html .= '<th>Ejecución media sobre gastos corrientes</th>';
    foreach($tmp as $clave=>$valor){
        $html .= '<td>'.($valor*100).'%</td>';
    }
    $html .= '</tr>';
    $html .='</tbody>';
    $html .= '</table></span>';
    $html .= '<br>';
}

$html .= '</span>';


$pdf->writeHTML($html, true, false, false, false, 'C');

/* PÁGINA 5 */

$pdf->AddPage();

$html = '<span style="text-align:justify;">
<h3>Deuda comercial pendiente de pago</h3>
';

for($i=0;$i<2;$i++){
    if($i==0) $tmp=$ccaa->getRDCPP();
    else if ($i==1) $tmp=$ccaaNac->getRDCPP();
    $html .= '<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th></th>';
    foreach($tmp as $array){
        $html .= '<th>'.$array[0].' (Mes '.$array[1].')</th>';
    }
    $html .= '</tr>';
    $html .='</thead>';
    $html .= '<tbody>';
    $html .= '<tr>';
    if($i==0) $html .= '<th>Porcentaje de pagos pendientes de deuda comercial</th>';
    else if ($i==1) $html .= '<th>Porcentaje medio de pagos pendientes de deuda comercial</th>';
    foreach($tmp as $array){
        $html .= '<td>'.($array[2]*100).'%</td>';
    }
    $html .= '</tr>';
    $html .='</tbody>';
    $html .= '</table></span>';
    $html .= '<br>';
}

$html .= '
<br>
<h3>Cumplimiento de pagos</h3>
';

for($i=0;$i<2;$i++){
    if($i==0) $tmp=$ccaa->getPagosObligaciones();
    else if ($i==1) $tmp=$ccaaNac->getPagosObligaciones();
    $html .= '<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th></th>';
    foreach($tmp as $clave=>$valor){
        $html .= '<th>'.$clave.'</th>';
    }
    $html .= '</tr>';
    $html .='</thead>';
    $html .= '<tbody>';
    $html .= '<tr>';
    if($i==0) $html .= '<th>Pagos sobre obligaciones reconocidas</th>';
    else if ($i==1) $html .= '<th>Porcentaje medio de gastos pagados</th>';
    foreach($tmp as $clave=>$valor){
        $html .= '<td>'.($valor*100).'%</td>';
    }
    $html .= '</tr>';
    $html .='</tbody>';
    $html .= '</table></span>';
    $html .= '<br>';
}

$html .= '
<br>
<h3>Gestión tributaria</h3>
';

for($i=0;$i<2;$i++){
    if($i==0) $tmp=$ccaa->getREficaciaRec();
    else if ($i==1) $tmp=$ccaaNac->getREficaciaRec();
    $html .= '<span style="text-align:left"><table cellspacing="0" cellpadding="5" border="1">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th></th>';
    foreach($tmp as $clave=>$valor){
        $html .= '<th>'.$clave.'</th>';
    }
    $html .= '</tr>';
    $html .='</thead>';
    $html .= '<tbody>';
    $html .= '<tr>';
    if($i==0) $html .= '<th>Eficacia recaudatoria</th>';
    else if ($i==1) $html .= '<th>Eficacia media recaudatoria</th>';
    foreach($tmp as $clave=>$valor){
        $html .= '<td>'.($valor*100).'%</td>';
    }
    $html .= '</tr>';
    $html .='</tbody>';
    $html .= '</table></span>';
    $html .= '<br><br>';
}

$html .= '</span>';


$pdf->writeHTML($html, true, false, false, false, 'C');

/* EXPORTACIÓN DEL ARCHIVO */

$pdf->lastPage();
ob_end_clean();

$pdf->Output('Informe_comunidad_autonoma.pdf', 'I');

?>