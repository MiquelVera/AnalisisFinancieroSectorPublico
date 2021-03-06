<?php

require_once('includesWeb/config.php');

class DAOConsultorDiputacion {

    public function getGeneralInfo($nombre){
        $db = getConexionBD();
        $sql = "SELECT CODIGO, NOMBRE, CIF, AUTONOMIA, PROVINCIA, TIPOVIA, NUMVIA, NOMBREVIA, TELEFONO, CODPOSTAL, FAX, MAIL, WEB FROM diputaciones WHERE NOMBRE = '$nombre'";
        $result = mysqli_query($db, $sql);
        if(!$result || mysqli_num_rows($result)==0){
            return false;
        }
        $diputacion = new Diputacion();
        $diputacion_res = mysqli_fetch_array($result);

        $diputacion->setCodigo($diputacion_res['CODIGO']);
        $diputacion->setNombre($diputacion_res['NOMBRE']);
        $diputacion->setCif($diputacion_res['CIF']);

        $ccaaCode = $diputacion_res['AUTONOMIA'];
        $sql = "SELECT NOMBRE FROM ccaas WHERE CODIGO = '$ccaaCode'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $autonomia = mysqli_fetch_assoc($result);
        $diputacion->setAutonomia($autonomia['NOMBRE']);

        $provinciaCode = $diputacion_res['PROVINCIA'];
        $sql = "SELECT NOMBRE FROM provincias WHERE CODIGO = '$provinciaCode'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $provincia = mysqli_fetch_assoc($result);
        $diputacion->setProvincia($provincia['NOMBRE']);
        $diputacion->setTipoVia($diputacion_res['TIPOVIA']);

        $diputacion->setNumVia($diputacion_res['NUMVIA']);
        $diputacion->setNombreVia($diputacion_res['NOMBREVIA']);
        $diputacion->setTelefono($diputacion_res['TELEFONO']);
        $diputacion->setCodigoPostal($diputacion_res['CODPOSTAL']);
        $diputacion->setFax($diputacion_res['FAX']);
        $diputacion->setMail($diputacion_res['MAIL']);
        $diputacion->setWeb($diputacion_res['WEB']);

        $cod = $diputacion_res['CODIGO'];
        $sql = "SELECT DISTINCT ANHO, RATING, TENDENCIA FROM scoring_dip WHERE CODIGO = '$cod'  AND RATING IS NOT NULL AND TENDENCIA IS NOT NULL ORDER BY ANHO DESC LIMIT 2";
        $scoring = mysqli_fetch_assoc($result);
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ratings=array();
        $tendencias=array();
        while($scoring = mysqli_fetch_assoc($result)){
            $key=$scoring['ANHO'];
            $value=$scoring['RATING'];
            $ratings[$key]=$value;

            $key=$scoring['ANHO'];
            $value=$scoring['TENDENCIA'];
            $tendencias[$key]=$value;
        }
        $diputacion->setScoring($ratings);
        $diputacion->setTendencia($tendencias);
        

        return $diputacion;
    }


    public function getIngresos($codigo, $year){
        $db = getConexionBD();

        $diputacion = new Diputacion();
        /* INGRESOS */
        //Impuestos Directos
        $sql = "SELECT DERE FROM cuentas_dip_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR1'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $impuestos_directos1 = mysqli_fetch_assoc($result);
        $diputacion->setImpuestosDirectos1($impuestos_directos1['DERE']);


        //Impuestos Indirectos
        $sql = "SELECT DERE FROM cuentas_dip_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR2'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $impuestos_indirectos1 = mysqli_fetch_assoc($result);
        $diputacion->setImpuestosIndirectos1($impuestos_indirectos1['DERE']);


        //Tasas Precios Otros
        $sql = "SELECT DERE FROM cuentas_dip_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR3'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $tasas_precios_otros1 = mysqli_fetch_assoc($result);
        $diputacion->setTasasPreciosOtros1($tasas_precios_otros1['DERE']);


        //Transferencias Corrientes
        $sql = "SELECT DERE FROM cuentas_dip_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR4'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $transferencias_corrientes1 = mysqli_fetch_assoc($result);
        $diputacion->setTransferenciasCorrientes1($transferencias_corrientes1['DERE']);


        //Ingresos Patrimoniales
        $sql = "SELECT DERE FROM cuentas_dip_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR5'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ingresos_patrimoniales1 = mysqli_fetch_assoc($result);
        $diputacion->setIngresosPatrimoniales1($ingresos_patrimoniales1['DERE']);


        //Total Ingresos Corrientes
        $totalIngresosCorrientes1 = floatval($impuestos_directos1['DERE']) + floatval($impuestos_indirectos1['DERE']) + floatval($tasas_precios_otros1['DERE']) + floatval($transferencias_corrientes1['DERE']) + floatval($ingresos_patrimoniales1['DERE']);
        $diputacion->setTotalIngresosCorrientes1($totalIngresosCorrientes1);

        
        //Enajenaci??n de Inversiones Reales
        $sql = "SELECT DERE FROM cuentas_dip_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR6'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $enajenacion_inversiones_reales1 = mysqli_fetch_assoc($result);
        $diputacion->setEnajenacionInversionesReales1($enajenacion_inversiones_reales1['DERE']);


        //Transferencias de Capital
        $sql = "SELECT DERE FROM cuentas_dip_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR7'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $transferencias_capital1 = mysqli_fetch_assoc($result);
        $diputacion->setTransferenciasCapital1($transferencias_capital1['DERE']);

        //Ingresos No Financieros
        $total_ingresos_no_corrientes1 = floatval($enajenacion_inversiones_reales1['DERE']) + floatval($transferencias_capital1['DERE']) + $totalIngresosCorrientes1;
        $diputacion->setTotalIngresosNoCorrientes1($total_ingresos_no_corrientes1);


        //Activos Financieros
        $sql = "SELECT DERE FROM cuentas_dip_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR8'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $activos_financieros1 = mysqli_fetch_assoc($result);
        $diputacion->setActivosFinancieros1($activos_financieros1['DERE']);

        //Pasivos Financieros
        $sql = "SELECT DERE FROM cuentas_dip_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR9'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $pasivos_financieros1 = mysqli_fetch_assoc($result);
        $diputacion->setPasivosFinancieros1($pasivos_financieros1['DERE']);


        //TOTAL INGRESOS
        $total_ingresos1 = $total_ingresos_no_corrientes1 + $activos_financieros1['DERE'] + $pasivos_financieros1['DERE'];
        $diputacion->setTotalIngresos1($total_ingresos1);

        return $diputacion;
    }

    public function getGastos($codigo, $year){
        $db = getConexionBD();
        $dip = new Diputacion();
        /* GASTOS */
        $sql = "SELECT OBLG FROM cuentas_dip_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST1'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $gastos_personal1 = mysqli_fetch_assoc($result);
        $dip->setGastosPersonal1($gastos_personal1['OBLG']);

        //Gastos Corrientes de Bienes y Servicios
        $sql = "SELECT OBLG FROM cuentas_dip_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST2'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $gastos_corrientes_bienes_servicios1 = mysqli_fetch_assoc($result);
        $dip->setGastosCorrientesBienesServicios1($gastos_corrientes_bienes_servicios1['OBLG']);

        //Gastos Financieros
        $sql = "SELECT OBLG FROM cuentas_dip_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST3'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $gastos_financieros1 = mysqli_fetch_assoc($result);
        $dip->setGastosFinancieros1($gastos_financieros1['OBLG']);

        //Transferencias Corrientes
        $sql = "SELECT OBLG FROM cuentas_dip_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST4'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $transferencias_corrientes_gastos1 = mysqli_fetch_assoc($result);
        $dip->setTransferenciasCorrientesGastos1($transferencias_corrientes_gastos1['OBLG']);

        //Fondo Contingencia
        $sql = "SELECT OBLG FROM cuentas_dip_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST5'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $fondo_contingencia1 = mysqli_fetch_assoc($result);
        $dip->setFondoContingencia1($fondo_contingencia1['OBLG']);

        //Total Gastos Corrientes
        $total_gastos_corrientes1 = floatval($gastos_personal1['OBLG']) + floatval($gastos_corrientes_bienes_servicios1['OBLG']) + floatval($gastos_financieros1['OBLG']) + floatval($transferencias_corrientes_gastos1['OBLG']) + floatval($fondo_contingencia1['OBLG']);
        $dip->setTotalGastosCorrientes1($total_gastos_corrientes1);

        //Inversiones Reales
        $sql = "SELECT OBLG FROM cuentas_dip_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST6'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $inversiones_reales1 = mysqli_fetch_assoc($result);
        $dip->setInversionesReales1($inversiones_reales1['OBLG']);

        //Transferencias de Capital
        $sql = "SELECT OBLG FROM cuentas_dip_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST7'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $transferencias_capital_gastos1 = mysqli_fetch_assoc($result);
        $dip->setTransferenciasCapitalGastos1($transferencias_capital_gastos1['OBLG']);

        //Gastos No Financieros
        $total_gastos_no_corrientes1 = doubleval($inversiones_reales1['OBLG']) + doubleval($transferencias_capital_gastos1['OBLG']) + $total_gastos_corrientes1;
        $dip->setTotalGastosNoFinancieros1($total_gastos_no_corrientes1);

        //Activos Financieros
        $sql = "SELECT OBLG FROM cuentas_dip_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST8'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $activos_financieros_gastos1 = mysqli_fetch_assoc($result);
        $dip->setActivosFinancierosGastos1($activos_financieros_gastos1['OBLG']);

        //Pasivos Financieros
        $sql = "SELECT OBLG FROM cuentas_dip_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST9'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $pasivos_financieros_gastos1 = mysqli_fetch_assoc($result);
        $dip->setPasivosFinancierosGastos1($pasivos_financieros_gastos1['OBLG']);

        //TOTAL GASTOS

        $total_gastos1 = floatval($total_gastos_no_corrientes1) + floatval($activos_financieros_gastos1['OBLG']) + floatval($pasivos_financieros_gastos1['OBLG']);
        $dip->setTotalGastos1($total_gastos1);


        return $dip;
    }

    public function getEndeudamiento($codigo, $year) {
        $db = getConexionBD();
        $dip = new Diputacion;

        //Deuda Financiera
        $sql = "SELECT DEUDAFIN FROM deudas_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $deuda_financiera = mysqli_fetch_assoc($result);
        
        $dip->setDeudaFinanciera($deuda_financiera['DEUDAFIN']);

        //Endeudamiento
        $sql = "SELECT R1 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $endeudamiento = mysqli_fetch_assoc($result);
        $dip->setEndeudamiento($endeudamiento['R1']);

        //Endeudamiento Media Nacional
        $sql = "SELECT R1_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $endeudamiento_media_nacional = mysqli_fetch_assoc($result);
        $dip->setEndeudamientoMediaDiputaciones($endeudamiento_media_nacional['R1_NAC']);
        

        return $dip;

    } 

    public function getSostenibilidad($codigo, $year) {
        $db = getConexionBD();
        $dip = new Diputacion;

 
        //Sostenibilidad Financiera
        $sql = "SELECT R2 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $sostenibilidad_financiera = mysqli_fetch_assoc($result);
        $dip->setSostenibilidadFinanciera($sostenibilidad_financiera['R2']);

        //Sostenibilidad Financiera Media Diputaciones
        $sql = "SELECT R2_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $sostenibilidad_financiera_media = mysqli_fetch_assoc($result);
        $dip->setSostenibilidadFinancieraMediaDiputaciones($sostenibilidad_financiera_media['R2_NAC']);

        //Apalancamiento Operativo
        $sql = "SELECT R3 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $apalancamiento_operativo = mysqli_fetch_assoc($result);
        $dip->setApalancamientoOperativo($apalancamiento_operativo['R3']);

        //Apalancamiento Operativo Media Diputaciones
        $sql = "SELECT R3_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $apalancamiento_operativo_media = mysqli_fetch_assoc($result);
        $dip->setApalancamientoOperativoMediaDiputaciones($apalancamiento_operativo_media['R3_NAC']);

        //Sostenibilidad de la Deuda
        $sql = "SELECT R4 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $sostenibilidad_deuda = mysqli_fetch_assoc($result);
        $dip->setSostenibilidadDeuda($sostenibilidad_deuda['R4']);


        //Sostenibilidad de la Deuda Media Diputaciones
        $sql = "SELECT R4_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $sostenibilidad_deuda_media = mysqli_fetch_assoc($result);
        $dip->setSostenibilidadDeudaMediaDiputaciones($sostenibilidad_deuda_media['R4_NAC']);


        return $dip;

    }


    public function getLiquidez($codigo, $year) {
        $db = getConexionBD();
        $dip = new Diputacion;

        //Fondos Liquidos
        $sql = "SELECT FONDLIQUIDOS FROM deudas_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $fondos_liquidos = mysqli_fetch_assoc($result);
        $dip->setFondosLiquidos($fondos_liquidos['FONDLIQUIDOS']);
        
        //Remanente Tesoreria Gastos Generales
        $sql = "SELECT R5 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $remanente_tesoreria_gastos_generales = mysqli_fetch_assoc($result);
        $dip->setRemanenteTesoreriaGastosGenerales($remanente_tesoreria_gastos_generales['R5']);

        //Remanente Tesoreria Gastos Generales Media Diputaciones
        $sql = "SELECT R5_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $remanente_tesoreria_gastos_generales_media_diputaciones = mysqli_fetch_assoc($result);
        $dip->setRemanenteTesoreriaGastosGeneralesMediaDiputaciones($remanente_tesoreria_gastos_generales_media_diputaciones['R5_NAC']);

        //Liquidez Inmediata
        $sql = "SELECT R6 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $liquidez_inmediata = mysqli_fetch_assoc($result);
        $dip->setLiquidezInmediata($liquidez_inmediata['R6']);

        //Solvencia Corto Plazo Media Diputaciones
        $sql = "SELECT R6_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $solvencia_corto_plazo_media_diputaciones = mysqli_fetch_assoc($result);
        $dip->setSolvenciaCortoPlazoMediaDiputaciones($solvencia_corto_plazo_media_diputaciones['R6_NAC']);

        //Solvencia Corto Plazo
        $sql = "SELECT R7 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $solvencia_corto_plazo = mysqli_fetch_assoc($result);
        $dip->setSolvenciaCortoPlazo($solvencia_corto_plazo['R7']);

        //Solvencia Corto Plazo Media Diputaciones 2
        $sql = "SELECT R7_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $solvencia_corto_plazo_media_diputaciones2 = mysqli_fetch_assoc($result);
        $dip->setSolvenciaCortoPlazoMediaDiputaciones2($solvencia_corto_plazo_media_diputaciones2['R7_NAC']);


        return $dip;

    }

    public function getEficiencia($codigo, $year) {
        $db = getConexionBD();
        $dip = new Diputacion;

        //Eficiencia
        $sql = "SELECT R8 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $eficiencia = mysqli_fetch_assoc($result);
        $dip->setEficiencia($eficiencia['R8']);

        //Eficiencia Media Diputaciones
        $sql = "SELECT R8_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $eficiencia_media_diputaciones = mysqli_fetch_assoc($result);
        $dip->setEficienciaMediaDiputaciones($eficiencia_media_diputaciones['R8_NAC']);

        return $dip;

    }

    public function getGestionPresupuestaria($codigo, $year) {
        $db = getConexionBD();
        $dip = new Diputacion;

        //EjecucionIngresosCorrientes
        $sql = "SELECT R9 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ejecucion_ingresos_corrientes = mysqli_fetch_assoc($result);
        $dip->setEjecucionIngresosCorrientes($ejecucion_ingresos_corrientes['R9']);

        //EjecucionIngresosCorrientesMediaDiputaciones
        $sql = "SELECT R9_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ejecucion_ingresos_corrientes_media_diputaciones = mysqli_fetch_assoc($result);
        $dip->setEjecucionIngresosCorrientesMediaDiputaciones($ejecucion_ingresos_corrientes_media_diputaciones['R9_NAC']);

        //EjecucionGastosCorrientes
        $sql = "SELECT R10 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ejecucion_gastos_corrientes = mysqli_fetch_assoc($result);
        $dip->setEjecucionGastosCorrientes($ejecucion_gastos_corrientes['R10']);

        //EjecucionGastosCorrientesMediaDiputaciones
        $sql = "SELECT R10_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ejecucion_gastos_corrientes_media_diputaciones = mysqli_fetch_assoc($result);
        $dip->setEjecucionGastosCorrientesMediaDiputaciones($ejecucion_gastos_corrientes_media_diputaciones['R10_NAC']);


        return $dip;

    }

    public function getCumplimientoPagos($codigo, $year) {
        $db = getConexionBD();
        $dip = new Diputacion;

        //DeudaComercial
        $sql = "SELECT DEUDACOM FROM deudas_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $deuda_comercial = mysqli_fetch_assoc($result);
        $dip->setDeudaComercial($deuda_comercial['DEUDACOM']);

        //PeriodoMedioPagos
        $sql = "SELECT R11 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $periodo_medio_pagos = mysqli_fetch_assoc($result);
        $dip->setPeriodoMedioPagos($periodo_medio_pagos['R11']);

        //PeriodoMedioPagosMediaDiputaciones
        $sql = "SELECT R11_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $periodo_medio_pagos_media_diputaciones = mysqli_fetch_assoc($result);
        $dip->setPeriodoMedioPagosMediaDiputaciones($periodo_medio_pagos_media_diputaciones['R11_NAC']);

        //PagosSobreObligacionesReconocidas
        $sql = "SELECT R12 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $pagos_sobre_obligaciones_reconocidas = mysqli_fetch_assoc($result);
        $dip->setPagosSobreObligacionesReconocidas($pagos_sobre_obligaciones_reconocidas['R12']);

        //PagosSobreObligacionesReconocidasMediaDiputaciones
        $sql = "SELECT R12_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $pagos_sobre_obligaciones_reconocidas_media_diputaciones = mysqli_fetch_assoc($result);
        $dip->setPagosSobreObligacionesReconocidasMediaDiputaciones($pagos_sobre_obligaciones_reconocidas_media_diputaciones['R12_NAC']);



        return $dip;

    }

    public function getGestionTributaria($codigo, $year) {
        $db = getConexionBD();
        $dip = new Diputacion;

        //DerechosPendientesCobro
        $sql = "SELECT DERPENDCOBRO FROM deudas_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $derechos_pendientes_cobro = mysqli_fetch_assoc($result);
        $dip->setDerechosPendientesCobro($derechos_pendientes_cobro['DERPENDCOBRO']);

        //EficaciaRecaudatoria
        $sql = "SELECT R13 FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $eficacia_recaudatoria = mysqli_fetch_assoc($result);
        $dip->setEficaciaRecaudatoria($eficacia_recaudatoria['R13']);

        //EficaciaRecaudatoriaMediaDiputaciones
        $sql = "SELECT R13_NAC FROM scoring_dip WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $eficacia_recaudatoria_media_diputaciones = mysqli_fetch_assoc($result);
        $dip->setEficaciaRecaudatoriaMediaDiputaciones($eficacia_recaudatoria_media_diputaciones['R13_NAC']);


        return $dip;
    }

    public function consultarDIPs($scoring, $poblacion, $endeudamiento, $ahorro_neto, $fondliq, $choice, $anho, $from, $to, $autonomia, $pmp, $ingrnofin, $gasto, $checked_boxes){
        $db = getConexionBD();
        $conditions = "";
        $returning_values="";
        $joins="";
        $order_by="";
        $group_by="";
        $having="";

        // Checkboxes
        if($checked_boxes[0]){ //endeudamiento
            if($joins!=""){ //significa que el  usuario ha pedido scoring aparte de poblacion
                if($conditions!=""){
                    $conditions = $conditions." AND ";
                }
                if(strpos($joins, ' cuentas_dip_pmp ')!==false) $conditions = $conditions . "scoring_dip.ANHO = cuentas_dip_pmp.ANHO";
                else if(strpos($joins, ' cuentas_dip_gastos ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = cuentas_dip_gastos.ANHO";
                else if(strpos($joins, ' cuentas_dip_ingresos ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = cuentas_dip_ingresos.ANHO";
                else if(strpos($joins, ' deudas_dip ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = deudas_dip.ANHO";
            }
            if(strpos($returning_values, 'ANHO')===false) $returning_values = $returning_values . ",scoring_dip.ANHO";
            $returning_values = $returning_values.",scoring_dip.R1";
            if(strpos($joins, 'INNER JOIN scoring_dip ON scoring_dip.CODIGO=diputaciones.CODIGO')===false) $joins = $joins . "INNER JOIN scoring_dip ON scoring_dip.CODIGO=diputaciones.CODIGO ";
            if(strpos($order_by, 'ANHO DESC')===false) $order_by = $order_by . "scoring_dip.ANHO DESC, ";
            if(strpos($group_by, 'ANHO')===false) $group_by = $group_by . ",scoring_dip.ANHO ";
        }
        if($checked_boxes[1]){ //ahorro neto
            if($joins!=""){ //significa que el  usuario ha pedido scoring aparte de poblacion
                if($conditions!=""){
                    $conditions = $conditions." AND ";
                }
                if(strpos($joins, ' cuentas_dip_pmp ')!==false) $conditions = $conditions . "scoring_dip.ANHO = cuentas_dip_pmp.ANHO";
                else if(strpos($joins, ' cuentas_dip_gastos ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = cuentas_dip_gastos.ANHO";
                else if(strpos($joins, ' cuentas_dip_ingresos ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = cuentas_dip_ingresos.ANHO";
                else if(strpos($joins, ' deudas_dip ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = deudas_dip.ANHO";
            }
            if(strpos($returning_values, 'ANHO')===false) $returning_values = $returning_values . ",scoring_dip.ANHO";
            $returning_values = $returning_values.", scoring_dip.R2";
            if(strpos($joins, 'INNER JOIN scoring_dip ON scoring_dip.CODIGO=diputaciones.CODIGO')===false) $joins = $joins . "INNER JOIN scoring_dip ON scoring_dip.CODIGO=diputaciones.CODIGO ";
            if(strpos($order_by, 'ANHO DESC')===false) $order_by = $order_by . "scoring_dip.ANHO DESC, ";
            if(strpos($group_by, 'ANHO')===false) $group_by = $group_by . ",scoring_dip.ANHO ";    
        }
        if($checked_boxes[2]){ //fondos liquidos (pendiente de cambiar)
            if($joins!=""){
                if($conditions!=""){
                    $conditions = $conditions." AND ";
                }
                if(strpos($joins, ' scoring_dip ')!==false)  $conditions = $conditions . "deudas_dip.ANHO = scoring_dip.ANHO";
                else if(strpos($joins, ' cuentas_dip_pmp ')!==false) $conditions = $conditions . "deudas_dip.ANHO = cuentas_dip_pmp.ANHO";
                else if(strpos($joins, ' cuentas_dip_gastos ')!==false)  $conditions = $conditions . "deudas_dip.ANHO = cuentas_dip_gastos.ANHO";
                else if(strpos($joins, ' cuentas_dip_ingresos ')!==false)  $conditions = $conditions . "deudas_dip.ANHO = cuentas_dip_ingresos.ANHO";
            }
            if(strpos($returning_values, 'ANHO')===false) $returning_values = $returning_values . ",deudas_dip.ANHO";
            $returning_values = $returning_values.",deudas_dip.FONDLIQUIDOS";
            if(strpos($joins, 'INNER JOIN deudas_dip ON deudas_dip.CODIGO=diputaciones.CODIGO')===false) $joins = $joins . "INNER JOIN deudas_dip ON deudas_dip.CODIGO=diputaciones.CODIGO ";
            if(strpos($order_by, 'ANHO DESC')===false) $order_by = $order_by . "deudas_dip.ANHO DESC, ";
            if(strpos($group_by, 'ANHO')===false) $group_by = $group_by . ",deudas_dip.ANHO ";
        }
        if($checked_boxes[3]){ //pmp
            if($joins!=""){
                if($conditions!=""){
                    $conditions = $conditions." AND ";
                }
                if(strpos($joins, ' scoring_dip ')!==false)  $conditions = $conditions . "cuentas_dip_pmp.ANHO = scoring_dip.ANHO";
                else if(strpos($joins, ' cuentas_dip_gastos ')!==false)  $conditions = $conditions . "cuentas_dip_pmp.ANHO = cuentas_dip_gastos.ANHO";
                else if(strpos($joins, ' cuentas_dip_ingresos ')!==false)  $conditions = $conditions . "cuentas_dip_pmp.ANHO = cuentas_dip_ingresos.ANHO";
                else if(strpos($joins, ' deudas_dip ')!==false)  $conditions = $conditions . "cuentas_dip_pmp.ANHO = deudas_dip.ANHO";
            }
            if(strpos($returning_values, 'ANHO')===false) $returning_values = $returning_values . ",cuentas_dip_pmp.ANHO";
            if(strpos($returning_values, ', cuentas_dip_pmp.TRIMESTRE ')===false) $returning_values = $returning_values . ", cuentas_dip_pmp.TRIMESTRE";
            $returning_values = $returning_values.",cuentas_dip_pmp.PMP";
            if(strpos($joins, 'INNER JOIN cuentas_dip_pmp ON cuentas_dip_pmp.CODIGO=diputaciones.CODIGO')===false) $joins = $joins . "INNER JOIN cuentas_dip_pmp ON cuentas_dip_pmp.CODIGO=diputaciones.CODIGO ";
            if(strpos($joins, 'INNER JOIN (SELECT c3.CODIGO, c3.ANHO, MAX(c3.TRIMESTRE) MAX_TRIMESTRE FROM cuentas_dip_pmp c3 WHERE c3.PMP IS NOT NULL GROUP BY c3.CODIGO, c3.ANHO) c3 ON c3.CODIGO=cuentas_dip_pmp.CODIGO AND c3.ANHO = cuentas_dip_pmp.ANHO AND cuentas_dip_pmp.TRIMESTRE=c3.MAX_TRIMESTRE')===false) $joins = $joins . "INNER JOIN (SELECT c3.CODIGO, c3.ANHO, MAX(c3.TRIMESTRE) MAX_TRIMESTRE FROM cuentas_dip_pmp c3 WHERE c3.PMP IS NOT NULL GROUP BY c3.CODIGO, c3.ANHO) c3 ON c3.CODIGO=cuentas_dip_pmp.CODIGO AND c3.ANHO = cuentas_dip_pmp.ANHO AND cuentas_dip_pmp.TRIMESTRE=c3.MAX_TRIMESTRE ";
            if(strpos($order_by, 'ANHO DESC')===false) $order_by = $order_by . "cuentas_dip_pmp.ANHO DESC, ";
            if(strpos($group_by, 'ANHO')===false) $group_by = $group_by . ",cuentas_dip_pmp.ANHO ";    
        }
        if($checked_boxes[4]){ // nivel de ingresos no financieros
            if($joins!=""){
                if($conditions!=""){
                    $conditions = $conditions." AND ";
                }
                if(strpos($joins, ' scoring_dip ')!==false)  $conditions = $conditions . "cuentas_dip_ingresos.ANHO = scoring_dip.ANHO";
                else if(strpos($joins, ' cuentas_dip_pmp ')!==false)  $conditions = $conditions . "cuentas_dip_ingresos.ANHO = cuentas_dip_pmp.ANHO";
                else if(strpos($joins, ' cuentas_dip_gastos ')!==false)  $conditions = $conditions . "cuentas_dip_ingresos.ANHO = cuentas_dip_gastos.ANHO";
                else if(strpos($joins, ' deudas_dip ')!==false)  $conditions = $conditions . "cuentas_dip_ingresos.ANHO = deudas_dip.ANHO";
            }
            if(strpos($returning_values, 'ANHO')===false) $returning_values = $returning_values . ",cuentas_dip_ingresos.ANHO";
            $returning_values = $returning_values.",SUM(cuentas_dip_ingresos.DERE) AS SUMA_INGR";
            if(strpos($joins, 'INNER JOIN cuentas_dip_ingresos ON cuentas_dip_ingresos.CODIGO=diputaciones.CODIGO')===false) $joins = $joins . "INNER JOIN cuentas_dip_ingresos ON cuentas_dip_ingresos.CODIGO=diputaciones.CODIGO ";
            if(strpos($order_by, 'ANHO DESC')===false) $order_by = $order_by . "cuentas_dip_ingresos.ANHO DESC, ";
            if(strpos($group_by, 'ANHO')===false) $group_by = $group_by . ",cuentas_dip_ingresos.ANHO ";
        }

        // Filtros
        if(!empty($scoring)){
            if($conditions!=""){
                $conditions = $conditions." AND ";
            }
            if($joins!=""){
                $tmp=$conditions;
                if(strpos($joins, ' cuentas_dip_pmp ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = cuentas_dip_pmp.ANHO";
                else if(strpos($joins, ' cuentas_dip_gastos ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = cuentas_dip_gastos.ANHO";
                else if(strpos($joins, ' cuentas_dip_ingresos ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = cuentas_dip_ingresos.ANHO";
                else if(strpos($joins, ' deudas_dip ')!==false)  $conditions = $conditions . "scoring_dip.ANHO = deudas_dip.ANHO";
                if($tmp!=$conditions) $conditions = $conditions." AND ";
            }
            if(strpos($returning_values, 'ANHO')===false) $returning_values = $returning_values . ",scoring_dip.ANHO";
            $conditions = $conditions."scoring_dip.RATING = '$scoring' ";

            $returning_values = $returning_values.",scoring_dip.ANHO, scoring_dip.RATING";
            if(strpos($joins, 'INNER JOIN scoring_dip ON scoring_dip.CODIGO=diputaciones.CODIGO')===false) $joins = $joins . "INNER JOIN scoring_dip ON scoring_dip.CODIGO=diputaciones.CODIGO ";
            $order_by = $order_by . "scoring_dip.ANHO DESC, ";
            $group_by = $group_by.",scoring_dip.ANHO";
        
        }   
        
        if(!empty($autonomia)){
            if($conditions!=""){
                $conditions = $conditions . "AND ";
            }
            $conditions = $conditions."diputaciones.AUTONOMIA = '$autonomia' ";
            $returning_values = $returning_values.",diputaciones.AUTONOMIA";
        }

        if(!empty($gasto)){
            if($conditions!=""){
                $conditions = $conditions . " AND ";
            }
            if($joins!=""){
                $tmp=$conditions;
                if(strpos($joins, ' scoring_dip ')!==false)  $conditions = $conditions . "cuentas_dip_gastos.ANHO = scoring_dip.ANHO";
                else if(strpos($joins, ' cuentas_dip_pmp ')!==false)  $conditions = $conditions . "cuentas_dip_gastos.ANHO = cuentas_dip_pmp.ANHO";
                else if(strpos($joins, ' cuentas_dip_ingresos ')!==false)  $conditions = $conditions . "cuentas_dip_gastos.ANHO = cuentas_dip_ingresos.ANHO";
                else if(strpos($joins, ' deudas_dip ')!==false)  $conditions = $conditions . "cuentas_dip_gastos.ANHO = deudas_dip.ANHO";
                if($tmp!=$conditions) $conditions = $conditions." AND ";
            }
            if($gasto=='personal'){
                $conditions = $conditions."(cuentas_dip_gastos.TIPO) ='PARTIDAGAST1' ";
            }
            else if($gasto=='bienesservicios'){
                $conditions = $conditions."(cuentas_dip_gastos.TIPO) ='PARTIDAGAST2' ";
            }
            else if($gasto=='financieros'){
                $conditions = $conditions."(cuentas_dip_gastos.TIPO) ='PARTIDAGAST3' ";
            }
            else if($gasto=='inversiones'){
                $conditions = $conditions."(cuentas_dip_gastos.TIPO) ='PARTIDAGAST6' ";
            }
            if(strpos($returning_values, 'ANHO')===false) $returning_values = $returning_values . ",cuentas_dip_gastos.ANHO";
            $returning_values = $returning_values.",cuentas_dip_gastos.TIPO, cuentas_dip_gastos.OBLG";
            if(strpos($joins, 'INNER JOIN cuentas_dip_gastos ON cuentas_dip_gastos.CODIGO=diputaciones.CODIGO')===false) $joins = $joins . "INNER JOIN cuentas_dip_gastos ON cuentas_dip_gastos.CODIGO=diputaciones.CODIGO ";
            if(strpos($order_by, 'ANHO DESC')===false) $order_by = $order_by . "cuentas_dip_gastos.ANHO DESC, ";
            if(strpos($group_by, 'ANHO')===false) $group_by = $group_by . ",cuentas_dip_gastos.ANHO ";
        }

        if(!empty($choice)){
            $anhoref='';
            if(strpos($joins, ' scoring_dip ')!==false)  $anhoref="scoring_dip.ANHO";
            else if(strpos($joins, ' cuentas_dip_pmp ')!==false)  $anhoref="cuentas_dip_pmp.ANHO";
            else if(strpos($joins, ' cuentas_dip_ingresos ')!==false)  $anhoref="cuentas_dip_ingresos.ANHO";
            else if(strpos($joins, ' cuentas_dip_gastos ')!==false) $anhoref="cuentas_dip_gastos.ANHO";
            else if(strpos($joins, ' deudas_dip ')!==false)  $anhoref="deudas_dip.ANHO";
            else {
                $anhoref="scoring_dip.ANHO";
                $joins=$joins."INNER JOIN scoring_dip ON scoring_dip.CODIGO=diputaciones.CODIGO";
            }
            if($choice =='SelectYear'){
                if(!empty($anho)){
                    if($conditions!=""){
                        $conditions = $conditions . "AND ";
                    }
                    $conditions = $conditions."$anhoref = '$anho' ";
                }
            }
            else {
                if(!empty($from) && !empty($to)){
                    if($conditions!=""){
                        $conditions = $conditions . "AND ";
                    }
                    $conditions = $conditions."$anhoref BETWEEN '$from' AND '$to' ";
                }
                else if(!empty($from) && empty($to)){
                    
                    if($conditions!=""){
                        $conditions = $conditions . "AND ";
                    }
                    $conditions = $conditions."$anhoref >= '$from' ";
                }
                else if(empty($from) && !empty($to)){
                    
                    if($conditions!=""){
                        $conditions = $conditions . "AND ";
                    }
                    $conditions = $conditions."$anhoref <= '$to' ";
                }
            }
            if(strpos($returning_values, 'ANHO')===false) $returning_values = $returning_values . ",$anhoref";
            if(strpos($order_by, 'ANHO DESC')===false) $order_by = $order_by . "$anhoref DESC, ";
            if(strpos($group_by, 'ANHO')===false) $group_by = $group_by . ",$anhoref ";
        }   

        if($conditions!=""){
            $conditions =" WHERE ".$conditions;
        }

        if($having!=""){
            $having =" HAVING ".$having;
        }

        //$sql = "SELECT DISTINCT(diputaciones.CODIGO), diputaciones.NOMBRE, scoring_dip.ANHO $returning_values FROM diputaciones INNER JOIN scoring_dip ON diputaciones.CODIGO = scoring_dip.CODIGO INNER JOIN deudas_dip ON deudas_dip.CODIGO = diputaciones.CODIGO $conditions ORDER BY scoring_dip.ANHO DESC, diputaciones.CODIGO ASC";
        //$sql = "SELECT diputaciones.CODIGO, diputaciones.NOMBRE, scoring_dip.ANHO $returning_values FROM diputaciones INNER JOIN scoring_dip ON diputaciones.CODIGO = scoring_dip.CODIGO $joins $conditions GROUP BY diputaciones.CODIGO, scoring_dip.ANHO ORDER BY scoring_dip.ANHO DESC, diputaciones.CODIGO ASC";
        $sql = "SELECT diputaciones.CODIGO, diputaciones.NOMBRE $returning_values FROM diputaciones $joins $conditions GROUP BY diputaciones.CODIGO $group_by $having ORDER BY $order_by diputaciones.CODIGO ASC";

        //echo $sql;
        $result = mysqli_query($db, $sql);
        if(!$result){
            return false;
        }
        $elements=array();
        while($resultado = mysqli_fetch_assoc($result)){
            $elements2 = array();
            $dip = new Diputacion();
            $dip->setNombre($resultado['NOMBRE']);
            if(!empty($resultado['RATING'])) $dip->setScoring($resultado['RATING']);
            if(!empty($resultado['R1']))$dip->setEndeudamiento($resultado['R1']);
            if(!empty($resultado['R2']))$dip->setSostenibilidadFinanciera($resultado['R2']);
            if(!empty($resultado['FONDLIQUIDOS']))$dip->setLiquidezInmediata($resultado['FONDLIQUIDOS']);
            if(!empty($resultado['PMP']))$dip->setPeriodoMedioPagos($resultado['PMP']);
            if(!empty($resultado['SUMA_INGR']))$dip->setTotalIngresosNoCorrientes1($resultado['SUMA_INGR']);
            if(!empty($resultado['OBLG'])) {
                if($resultado['TIPO']=='PARTIDAGAST1') $dip->setGastosPersonal1($resultado['OBLG']);
                else if($resultado['TIPO']=='PARTIDAGAST2') $dip->setGastosCorrientesBienesServicios1($resultado['OBLG']);
                else if($resultado['TIPO']=='PARTIDAGAST3') $dip->setTransferenciasCorrientesGastos1($resultado['OBLG']);
                else if($resultado['TIPO']=='PARTIDAGAST6') $dip->setInversionesReales1($resultado['OBLG']);
            }
            if(!empty($resultado['AUTONOMIA'])) {    
                $ccaaCode = $resultado['AUTONOMIA'];
                $sql = "SELECT NOMBRE FROM ccaas WHERE CODIGO = '$ccaaCode'";
                $resultCode = mysqli_query($db,$sql);
                if(!$resultCode){
                    return false;
                }
                $autonomia = mysqli_fetch_assoc($resultCode);
                $dip->setAutonomia($autonomia['NOMBRE']);
            }
            if(!array_key_exists($resultado['ANHO'], $elements)){
                $elements[$resultado['ANHO']]=array();
            }
            ($elements[$resultado['ANHO']])[$resultado['CODIGO']]=$dip;
        }
        krsort($elements);
        $sum=0;
        while ($year_array = current($elements)) {
            ksort($elements[key($elements)]);
            $sum+=count($elements[key($elements)]);
            next($elements);
        }
        $elements['total']=$sum;
        
        return $elements;
    }   

}

?>