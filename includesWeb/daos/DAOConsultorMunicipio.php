<?php

class DAOConsultorMunicipio{

    public function getGeneralInfo($nombre){
        $db = getConexionBD();
        $sql = "SELECT CODIGO, CIF, NOMBRE, AUTONOMIA, PROVINCIA, NOMBREALCALDE, APELLIDO1ALCALDE, APELLIDO2ALCALDE, VIGENCIA, PARTIDO, TIPOVIA, NOMBREVIA, NUMVIA, CODPOSTAL, TELEFONO, FAX, WEB, MAIL FROM municipios WHERE NOMBRE = '$nombre'";
        $result = mysqli_query($db, $sql);
        if(!$result || mysqli_num_rows($result)==0){
            return false;
        }
        $municipio = new Municipio();
        $municipio_res = mysqli_fetch_assoc($result);

        $municipio->setCodigo($municipio_res['CODIGO']);
        $municipio->setCif($municipio_res['CIF']);
        $municipio->setNombre($municipio_res['NOMBRE']);
        
        $ccaaCode = $municipio_res['AUTONOMIA'];
        $sql = "SELECT NOMBRE FROM ccaas WHERE CODIGO = '$ccaaCode'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $autonomia = mysqli_fetch_assoc($result);
        $municipio->setAutonomia($autonomia['NOMBRE']);

        $provinciaCode = $municipio_res['PROVINCIA'];
        $sql = "SELECT NOMBRE FROM provincias WHERE CODIGO = '$provinciaCode'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $provincia = mysqli_fetch_array($result);

        $municipio->setProvincia($provincia['NOMBRE']);

        $municipio->setNombreAlcalde($municipio_res['NOMBREALCALDE']);
        $municipio->setApellido1($municipio_res['APELLIDO1ALCALDE']);
        $municipio->setApellido2($municipio_res['APELLIDO2ALCALDE']);
        $municipio->setVigencia($municipio_res['VIGENCIA']);
        $municipio->setPartido($municipio_res['PARTIDO']);
        $municipio->setTipoVia($municipio_res['TIPOVIA']);
        $municipio->setNombreVia($municipio_res['NOMBREVIA']);
        $municipio->setNumVia($municipio_res['NUMVIA']);
        $municipio->setCodigoPostal($municipio_res['CODPOSTAL']);
        $municipio->setTelefono($municipio_res['TELEFONO']);
        $municipio->setFax($municipio_res['FAX']);
        $municipio->setWeb($municipio_res['WEB']);
        $municipio->setMail($municipio_res['MAIL']);

        $cod = $municipio_res['CODIGO'];
        $sql = "SELECT DISTINCT ANHO, RATING, TENDENCIA FROM scoring_mun WHERE CODIGO = '$cod' AND RATING IS NOT NULL AND TENDENCIA IS NOT NULL ORDER BY ANHO DESC LIMIT 2";
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
        $municipio->setScoring($ratings);
        $municipio->setTendencia($tendencias);

        return $municipio;
    }

    public function getIngresos($codigo, $year){
        $db = getConexionBD();

        $municipio = new Municipio();
        /* INGRESOS */
        //Impuestos Directos
        $sql = "SELECT DERE FROM cuentas_mun_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR1'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $impuestos_directos1 = mysqli_fetch_assoc($result);
        $municipio->setImpuestosDirectos1($impuestos_directos1['DERE']);


        //Impuestos Indirectos
        $sql = "SELECT DERE FROM cuentas_mun_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR2'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $impuestos_indirectos1 = mysqli_fetch_assoc($result);
        $municipio->setImpuestosIndirectos1($impuestos_indirectos1['DERE']);


        //Tasas Precios Otros
        $sql = "SELECT DERE FROM cuentas_mun_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR3'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $tasas_precios_otros1 = mysqli_fetch_assoc($result);
        $municipio->setTasasPreciosOtros1($tasas_precios_otros1['DERE']);


        //Transferencias Corrientes
        $sql = "SELECT DERE FROM cuentas_mun_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR4'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $transferencias_corrientes1 = mysqli_fetch_assoc($result);
        $municipio->setTransferenciasCorrientes1($transferencias_corrientes1['DERE']);


        //Ingresos Patrimoniales
        $sql = "SELECT DERE FROM cuentas_mun_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR5'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ingresos_patrimoniales1 = mysqli_fetch_assoc($result);
        $municipio->setIngresosPatrimoniales1($ingresos_patrimoniales1['DERE']);


        //Total Ingresos Corrientes
        $totalIngresosCorrientes1 = floatval($impuestos_directos1['DERE']) + floatval($impuestos_indirectos1['DERE']) + floatval($tasas_precios_otros1['DERE']) + floatval($transferencias_corrientes1['DERE']) + floatval($ingresos_patrimoniales1['DERE']);
        $municipio->setTotalIngresosCorrientes1($totalIngresosCorrientes1);

        
        //Enajenación de Inversiones Reales
        $sql = "SELECT DERE FROM cuentas_mun_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR6'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $enajenacion_inversiones_reales1 = mysqli_fetch_assoc($result);
        $municipio->setEnajenacionInversionesReales1($enajenacion_inversiones_reales1['DERE']);


        //Transferencias de Capital
        $sql = "SELECT DERE FROM cuentas_mun_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR7'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $transferencias_capital1 = mysqli_fetch_assoc($result);
        $municipio->setTransferenciasCapital1($transferencias_capital1['DERE']);

        //Ingresos No Financieros
        $total_ingresos_no_corrientes1 = floatval($enajenacion_inversiones_reales1['DERE']) + floatval($transferencias_capital1['DERE']);
        $municipio->setTotalIngresosNoCorrientes1($total_ingresos_no_corrientes1);


        //Activos Financieros
        $sql = "SELECT DERE FROM cuentas_mun_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR8'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $activos_financieros1 = mysqli_fetch_assoc($result);
        $municipio->setActivosFinancieros1($activos_financieros1['DERE']);

        //Pasivos Financieros
        $sql = "SELECT DERE FROM cuentas_mun_ingresos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAINGR9'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $pasivos_financieros1 = mysqli_fetch_assoc($result);
        $municipio->setPasivosFinancieros1($pasivos_financieros1['DERE']);


        //TOTAL INGRESOS
        $total_ingresos1 = $totalIngresosCorrientes1 + $total_ingresos_no_corrientes1 + $activos_financieros1['DERE'] + $pasivos_financieros1['DERE'];
        $municipio->setTotalIngresos1($total_ingresos1);

        return $municipio;
    }

    public function getGastos($codigo, $year){
        $db = getConexionBD();
        $mun = new Municipio();
        /* GASTOS */
        $sql = "SELECT OBLG FROM cuentas_mun_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST1'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $gastos_personal1 = mysqli_fetch_assoc($result);
        $mun->setGastosPersonal1($gastos_personal1['OBLG']);

        //Gastos Corrientes de Bienes y Servicios
        $sql = "SELECT OBLG FROM cuentas_mun_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST2'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $gastos_corrientes_bienes_servicios1 = mysqli_fetch_assoc($result);
        $mun->setGastosCorrientesBienesServicios1($gastos_corrientes_bienes_servicios1['OBLG']);

        //Gastos Financieros
        $sql = "SELECT OBLG FROM cuentas_mun_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST3'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $gastos_financieros1 = mysqli_fetch_assoc($result);
        $mun->setGastosFinancieros1($gastos_financieros1['OBLG']);

        //Transferencias Corrientes
        $sql = "SELECT OBLG FROM cuentas_mun_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST4'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $transferencias_corrientes_gastos1 = mysqli_fetch_assoc($result);
        $mun->setTransferenciasCorrientesGastos1($transferencias_corrientes_gastos1['OBLG']);

        //Fondo Contingencia
        $sql = "SELECT OBLG FROM cuentas_mun_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST5'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $fondo_contingencia1 = mysqli_fetch_assoc($result);
        $mun->setFondoContingencia1($fondo_contingencia1['OBLG']);

        //Total Gastos Corrientes
        $total_gastos_corrientes1 = floatval($gastos_personal1['OBLG']) + floatval($gastos_corrientes_bienes_servicios1['OBLG']) + floatval($gastos_financieros1['OBLG']) + floatval($transferencias_corrientes_gastos1['OBLG']) + floatval($fondo_contingencia1['OBLG']);
        $mun->setTotalGastosCorrientes1($total_gastos_corrientes1);

        //Inversiones Reales
        $sql = "SELECT OBLG FROM cuentas_mun_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST6'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $inversiones_reales1 = mysqli_fetch_assoc($result);
        $mun->setInversionesReales1($inversiones_reales1['OBLG']);

        //Transferencias de Capital
        $sql = "SELECT OBLG FROM cuentas_mun_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST7'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $transferencias_capital_gastos1 = mysqli_fetch_assoc($result);
        $mun->setTransferenciasCapitalGastos1($transferencias_capital_gastos1['OBLG']);

        //Gastos No Financieros
        $total_gastos_no_corrientes1 = floatval($inversiones_reales1['OBLG']) + floatval($transferencias_capital_gastos1['OBLG']);
        $mun->setTotalGastosNoFinancieros1($total_gastos_no_corrientes1);

        //Activos Financieros
        $sql = "SELECT OBLG FROM cuentas_mun_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST8'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $activos_financieros_gastos1 = mysqli_fetch_assoc($result);
        $mun->setActivosFinancierosGastos1($activos_financieros_gastos1['OBLG']);

        //Pasivos Financieros
        $sql = "SELECT OBLG FROM cuentas_mun_gastos WHERE CODIGO = '$codigo' AND ANHO = '$year' AND TIPO = 'PARTIDAGAST9'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $pasivos_financieros_gastos1 = mysqli_fetch_assoc($result);
        $mun->setPasivosFinancierosGastos1($pasivos_financieros_gastos1['OBLG']);

        //TOTAL GASTOS

        $total_gastos1 = floatval($total_gastos_corrientes1) + floatval($total_gastos_no_corrientes1) + floatval($activos_financieros_gastos1['OBLG']) + floatval($pasivos_financieros_gastos1['OBLG']);
        $mun->setTotalGastos1($total_gastos1);


        return $mun;
    }

    public function getEndeudamiento($codigo, $year) {
        $db = getConexionBD();
        $mun = new Municipio;

        //Deuda Financiera
        $sql = "SELECT DEUDAFIN FROM deudas_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $deuda_financiera = mysqli_fetch_assoc($result);
        
        $mun->setDeudaFinanciera($deuda_financiera['DEUDAFIN']);

        //Endeudamiento
        $sql = "SELECT R1 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $endeudamiento = mysqli_fetch_assoc($result);
        $mun->setEndeudamiento($endeudamiento['R1']);

        //Endeudamiento Media Nacional
        $sql = "SELECT R1_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $endeudamiento_media_nacional = mysqli_fetch_assoc($result);
        $mun->setEndeudamientoMediaDiputaciones($endeudamiento_media_nacional['R1_NAC']);
        

        return $mun;

    } 

    public function getSostenibilidad($codigo, $year) {
        $db = getConexionBD();
        $mun = new Municipio;
 
        //Sostenibilidad Financiera
        $sql = "SELECT R2 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $sostenibilidad_financiera = mysqli_fetch_assoc($result);
        $mun->setSostenibilidadFinanciera($sostenibilidad_financiera['R2']);

        //Sostenibilidad Financiera Media Diputaciones
        $sql = "SELECT R2_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $sostenibilidad_financiera_media = mysqli_fetch_assoc($result);
        $mun->setSostenibilidadFinancieraMediaDiputaciones($sostenibilidad_financiera_media['R2_NAC']);

        //Apalancamiento Operativo
        $sql = "SELECT R3 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $apalancamiento_operativo = mysqli_fetch_assoc($result);
        $mun->setApalancamientoOperativo($apalancamiento_operativo['R3']);

        //Apalancamiento Operativo Media Diputaciones
        $sql = "SELECT R3_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $apalancamiento_operativo_media = mysqli_fetch_assoc($result);
        $mun->setApalancamientoOperativoMediaDiputaciones($apalancamiento_operativo_media['R3_NAC']);

        //Sostenibilidad de la Deuda
        $sql = "SELECT R4 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $sostenibilidad_deuda = mysqli_fetch_assoc($result);
        $mun->setSostenibilidadDeuda($sostenibilidad_deuda['R4']);


        //Sostenibilidad de la Deuda Media Diputaciones
        $sql = "SELECT R4_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $sostenibilidad_deuda_media = mysqli_fetch_assoc($result);
        $mun->setSostenibilidadDeudaMediaDiputaciones($sostenibilidad_deuda_media['R4_NAC']);

        return $mun;

    }

    public function getLiquidez($codigo, $year) {
        $db = getConexionBD();
        $mun = new Municipio;

        //Fondos Liquidos
        $sql = "SELECT FONDLIQUIDOS FROM deudas_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $fondos_liquidos = mysqli_fetch_assoc($result);
        $mun->setFondosLiquidos($fondos_liquidos['FONDLIQUIDOS']);
        
        //Remanente Tesoreria Gastos Generales
        $sql = "SELECT R5 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $remanente_tesoreria_gastos_generales = mysqli_fetch_assoc($result);
        $mun->setRemanenteTesoreriaGastosGenerales($remanente_tesoreria_gastos_generales['R5']);

        //Remanente Tesoreria Gastos Generales Media Diputaciones
        $sql = "SELECT R5_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $remanente_tesoreria_gastos_generales_media_diputaciones = mysqli_fetch_assoc($result);
        $mun->setRemanenteTesoreriaGastosGeneralesMediaDiputaciones($remanente_tesoreria_gastos_generales_media_diputaciones['R5_NAC']);

        //Liquidez Inmediata
        $sql = "SELECT R6 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $liquidez_inmediata = mysqli_fetch_assoc($result);
        $mun->setLiquidezInmediata($liquidez_inmediata['R6']);

        //Solvencia Corto Plazo Media Diputaciones
        $sql = "SELECT R6_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $solvencia_corto_plazo_media_diputaciones = mysqli_fetch_assoc($result);
        $mun->setSolvenciaCortoPlazoMediaDiputaciones($solvencia_corto_plazo_media_diputaciones['R6_NAC']);

        //Solvencia Corto Plazo
        $sql = "SELECT R7 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $solvencia_corto_plazo = mysqli_fetch_assoc($result);
        $mun->setSolvenciaCortoPlazo($solvencia_corto_plazo['R7']);

        //Solvencia Corto Plazo Media Diputaciones 2
        $sql = "SELECT R7_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $solvencia_corto_plazo_media_diputaciones2 = mysqli_fetch_assoc($result);
        $mun->setSolvenciaCortoPlazoMediaDiputaciones2($solvencia_corto_plazo_media_diputaciones2['R7_NAC']);

        return $mun;

    }

    public function getEficiencia($codigo, $year) {
        $db = getConexionBD();
        $mun = new Municipio;

        //Eficiencia
        $sql = "SELECT R8 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $eficiencia = mysqli_fetch_assoc($result);
        $mun->setEficiencia($eficiencia['R8']);

        //Eficiencia Media Diputaciones
        $sql = "SELECT R8_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $eficiencia_media_diputaciones = mysqli_fetch_assoc($result);
        $mun->setEficienciaMediaDiputaciones($eficiencia_media_diputaciones['R8_NAC']);

        return $mun;
    }

    public function getGestionPresupuestaria($codigo, $year) {
        $db = getConexionBD();
        $mun = new Municipio;

        //EjecucionIngresosCorrientes
        $sql = "SELECT R9 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ejecucion_ingresos_corrientes = mysqli_fetch_assoc($result);
        $mun->setEjecucionIngresosCorrientes($ejecucion_ingresos_corrientes['R9']);

        //EjecucionIngresosCorrientesMediaDiputaciones
        $sql = "SELECT R9_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ejecucion_ingresos_corrientes_media_diputaciones = mysqli_fetch_assoc($result);
        $mun->setEjecucionIngresosCorrientesMediaDiputaciones($ejecucion_ingresos_corrientes_media_diputaciones['R9_NAC']);

        //EjecucionGastosCorrientes
        $sql = "SELECT R10 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ejecucion_gastos_corrientes = mysqli_fetch_assoc($result);
        $mun->setEjecucionGastosCorrientes($ejecucion_gastos_corrientes['R10']);

        //EjecucionGastosCorrientesMediaDiputaciones
        $sql = "SELECT R10_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $ejecucion_gastos_corrientes_media_diputaciones = mysqli_fetch_assoc($result);
        $mun->setEjecucionGastosCorrientesMediaDiputaciones($ejecucion_gastos_corrientes_media_diputaciones['R10_NAC']);

        return $mun;

    }

    public function getCumplimientoPagos($codigo, $year) {
        $db = getConexionBD();
        $mun = new Municipio;

        //DeudaComercial
        $sql = "SELECT DEUDACOM FROM deudas_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $deuda_comercial = mysqli_fetch_assoc($result);
        $mun->setDeudaComercial($deuda_comercial['DEUDACOM']);

        //PeriodoMedioPagos
        $sql = "SELECT R11 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $periodo_medio_pagos = mysqli_fetch_assoc($result);
        $mun->setPeriodoMedioPagos($periodo_medio_pagos['R11']);

        //PeriodoMedioPagosMediaDiputaciones
        $sql = "SELECT R11_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $periodo_medio_pagos_media_diputaciones = mysqli_fetch_assoc($result);
        $mun->setPeriodoMedioPagosMediaDiputaciones($periodo_medio_pagos_media_diputaciones['R11_NAC']);

        //PagosSobreObligacionesReconocidas
        $sql = "SELECT R12 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $pagos_sobre_obligaciones_reconocidas = mysqli_fetch_assoc($result);
        $mun->setPagosSobreObligacionesReconocidas($pagos_sobre_obligaciones_reconocidas['R12']);

        //PagosSobreObligacionesReconocidasMediaDiputaciones
        $sql = "SELECT R12_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $pagos_sobre_obligaciones_reconocidas_media_diputaciones = mysqli_fetch_assoc($result);
        $mun->setPagosSobreObligacionesReconocidasMediaDiputaciones($pagos_sobre_obligaciones_reconocidas_media_diputaciones['R12_NAC']);

        return $mun;
    }

    public function getGestionTributaria($codigo, $year) {
        $db = getConexionBD();
        $mun = new Municipio;

        //DerechosPendientesCobro
        $sql = "SELECT DERPENDCOBRO FROM deudas_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $derechos_pendientes_cobro = mysqli_fetch_assoc($result);
        $mun->setDerechosPendientesCobro($derechos_pendientes_cobro['DERPENDCOBRO']);

        //EficaciaRecaudatoria
        $sql = "SELECT R13 FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $eficacia_recaudatoria = mysqli_fetch_assoc($result);
        $mun->setEficaciaRecaudatoria($eficacia_recaudatoria['R13']);

        //EficaciaRecaudatoriaMediaDiputaciones
        $sql = "SELECT R13_NAC FROM scoring_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        $result = mysqli_query($db,$sql);
        if(!$result){
            return false;
        }
        $eficacia_recaudatoria_media_diputaciones = mysqli_fetch_assoc($result);
        $mun->setEficaciaRecaudatoriaMediaDiputaciones($eficacia_recaudatoria_media_diputaciones['R13_NAC']);

        return $mun;
    }

    /* Función para la página de consultas */
    public function consultarMUNs($scoring, $poblacion, $endeudamiento, $ahorro_neto, $fondliq, $choice, $anho, $from, $to, $autonomia, $provincia, $pmp, $ingrnofin, $gasto){
        $db = getConexionBD();
        $conditions = "";
        $returning_values="";
        $joins="";
        $having="";

        if(!empty($scoring)){
            if($conditions!=""){
                $conditions = $conditions . "AND ";
            }
            $conditions = $conditions."scoring_mun.RATING = '$scoring' ";
            $returning_values = $returning_values.",scoring_mun.RATING";
        }   
        
        if(!empty($poblacion)){
            if($conditions!=""){
                $conditions = $conditions . "AND ";
            }
            if($poblacion=='tramo1'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 0 AND 100 ";
            }
            else if($poblacion=='tramo2'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 100 AND 500 ";
            }
            else if($poblacion=='tramo3'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 500 AND 1000 ";
            }
            else if($poblacion=='tramo4'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 1000 AND 2000 ";
            }
            else if($poblacion=='tramo5'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 2000 AND 5000 ";
            }
            else if($poblacion=='tramo6'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 5000 AND 10000 ";
            }
            else if($poblacion=='tramo7'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 10000 AND 20000 ";
            }
            else if($poblacion=='tramo8'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 20000 AND 50000 ";
            }
            else if($poblacion=='tramo9'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 50000 AND 100000 ";
            }
            else if($poblacion=='tramo10'){
                $conditions = $conditions."(scoring_mun.POBLACION) BETWEEN 100000 AND 500000 ";
            }
            else if($poblacion=='tramo11'){
                $conditions = $conditions."(scoring_mun.POBLACION) > 500000 ";
            }
            $returning_values = $returning_values.",scoring_mun.POBLACION";
        }

        if(!empty($endeudamiento)){
            if($conditions!=""){
                $conditions = $conditions . "AND ";
            }
            if($endeudamiento=='tramo1'){
                $conditions = $conditions."(scoring_mun.R1*100) BETWEEN 0 AND 20 ";
            }
            else if($endeudamiento=='tramo2'){
                $conditions = $conditions."(scoring_mun.R1*100) BETWEEN 20 AND 40 ";
            }
            else if($endeudamiento=='tramo3'){
                $conditions = $conditions."(scoring_mun.R1*100) BETWEEN 40 AND 80 ";
            }
            else if($endeudamiento=='tramo4'){
                $conditions = $conditions."(scoring_mun.R1*100) BETWEEN 80 AND 120 ";
            }
            else if($endeudamiento=='tramo5'){
                $conditions = $conditions."(scoring_mun.R1*100) > 120 ";
            }
            $returning_values = $returning_values.",scoring_mun.R1";
        }
        
        if(!empty($ahorro_neto)){
            if($conditions!=""){
                $conditions = $conditions . "AND ";
            }
            if($ahorro_neto=='tramo1'){
                $conditions = $conditions."(scoring_mun.R2*100) < -20 ";
            }
            else if($ahorro_neto=='tramo2'){
                $conditions = $conditions."(scoring_mun.R2*100) BETWEEN -20 AND 0 ";
            }
            else if($ahorro_neto=='tramo3'){
                $conditions = $conditions."(scoring_mun.R2*100) BETWEEN 0 AND 20 ";
            }
            else if($ahorro_neto=='tramo4'){
                $conditions = $conditions."(scoring_mun.R2*100) BETWEEN 20 AND 50 ";
            }
            else if($ahorro_neto=='tramo5'){
                $conditions = $conditions."(scoring_mun.R2*100) > 50 ";
            }
            $returning_values = $returning_values.",scoring_mun.R2";
        }
        
        if(!empty($fondliq)){
            if($conditions!=""){
                $conditions = $conditions . "AND deudas_mun.ANHO = scoring_mun.ANHO AND ";
            }
            if($fondliq=='tramo1'){
                $conditions = $conditions."(deudas_mun.FONDLIQUIDOS) BETWEEN 0 AND 1000000 ";
            }
            else if($fondliq=='tramo2'){
                $conditions = $conditions."(deudas_mun.FONDLIQUIDOS) BETWEEN 1000000 AND 5000000 ";
            }
            else if($fondliq=='tramo3'){
                $conditions = $conditions."(deudas_mun.FONDLIQUIDOS) BETWEEN 5000000 AND 50000000 ";
            }
            else if($fondliq=='tramo4'){
                $conditions = $conditions."(deudas_mun.FONDLIQUIDOS) > 50000000 ";
            }
            $returning_values = $returning_values.",deudas_mun.FONDLIQUIDOS";
            if(strpos($joins, 'INNER JOIN deudas_mun ON deudas_mun.CODIGO=municipios.CODIGO')===false) $joins = $joins . "INNER JOIN deudas_mun ON deudas_mun.CODIGO=municipios.CODIGO ";
        }

        if(!empty($pmp)){
            if($conditions!=""){
                $conditions = $conditions . "AND cuentas_mun_general_pmp.ANHO = scoring_mun.ANHO AND ";
            }
            if($pmp=='tramo1'){
                $conditions = $conditions."(cuentas_mun_pmp.PMP) BETWEEN 0 AND 10 ";
            }
            else if($pmp=='tramo2'){
                $conditions = $conditions."(cuentas_mun_pmp.PMP) BETWEEN 10 AND 20 ";
            }
            else if($pmp=='tramo3'){
                $conditions = $conditions."(cuentas_mun_pmp.PMP) BETWEEN 20 AND 30 ";
            }
            else if($pmp=='tramo4'){
                $conditions = $conditions."(cuentas_mun_pmp.PMP) BETWEEN 30 AND 40 ";
            }
            else if($pmp=='tramo5'){
                $conditions = $conditions."(cuentas_mun_pmp.PMP) BETWEEN 40 AND 50 ";
            }
            else if($pmp=='tramo6'){
                $conditions = $conditions."(cuentas_mun_pmp.PMP) > 50 ";
            }
            if(strpos($returning_values, ', MAX(cuentas_mun_pmp.TRIMESTRE) as MAX_TRIMESTRE')===false) $returning_values = $returning_values . ", MAX(cuentas_mun_pmp.TRIMESTRE)";
            $returning_values = $returning_values.",cuentas_mun_pmp.PMP";
            if(strpos($joins, 'INNER JOIN cuentas_mun_pmp ON cuentas_mun_pmp.CODIGO=municipios.CODIGO')===false) $joins = $joins . "INNER JOIN cuentas_mun_pmp ON cuentas_mun_pmp.CODIGO=municipios.CODIGO ";
        }

        if(!empty($ingrnofin)){
            if($conditions!=""){
                $conditions = $conditions . "AND cuentas_mun_ingresos.ANHO = scoring_mun.ANHO AND ";
            }
            $conditions = $conditions."cuentas_mun_ingresos.TIPO='PARTIDAINGR1' OR cuentas_mun_ingresos.TIPO='PARTIDAINGR2' OR cuentas_mun_ingresos.TIPO='PARTIDAINGR3' OR cuentas_mun_ingresos.TIPO='PARTIDAINGR4' OR cuentas_mun_ingresos.TIPO='PARTIDAINGR5' ";
            if($ingrnofin=='tramo1'){
                $having = $having."SUMA_INGR BETWEEN 0 AND 1000000 ";
            }
            else if($ingrnofin=='tramo2'){
                $having = $having."SUMA_INGR BETWEEN 1000000 AND 5000000 ";
            }
            else if($ingrnofin=='tramo3'){
                $having = $having."SUMA_INGR BETWEEN 5000000 AND 50000000 ";
            }
            else if($ingrnofin=='tramo4'){
                $having = $having."SUMA_INGR > 50000000 ";
            }
            $returning_values = $returning_values.",SUM(cuentas_mun_ingresos.DERE) AS SUMA_INGR";
            if(strpos($joins, 'INNER JOIN cuentas_mun_ingresos ON cuentas_mun_ingresos.CODIGO=municipios.CODIGO')===false) $joins = $joins . "INNER JOIN cuentas_mun_ingresos ON cuentas_mun_ingresos.CODIGO=municipios.CODIGO ";
        }
        
        if(!empty($gasto)){
            if($conditions!=""){
                $conditions = $conditions . "AND cuentas_mun_gastos.ANHO = scoring_mun.ANHO AND ";
            }
            if($gasto=='personal'){
                $conditions = $conditions."(cuentas_mun_gastos.TIPO) ='PARTIDAGAST1' ";
            }
            else if($gasto=='bienesservicios'){
                $conditions = $conditions."(cuentas_mun_gastos.TIPO) ='PARTIDAGAST2' ";
            }
            else if($gasto=='financieros'){
                $conditions = $conditions."(cuentas_mun_gastos.TIPO) ='PARTIDAGAST3' ";
            }
            else if($gasto=='inversiones'){
                $conditions = $conditions."(cuentas_mun_gastos.TIPO) ='PARTIDAGAST6' ";
            }
            $returning_values = $returning_values.",cuentas_mun_gastos.TIPO, cuentas_mun_gastos.OBLG";
            if(strpos($joins, 'INNER JOIN cuentas_mun_gastos ON cuentas_mun_gastos.CODIGO=municipios.CODIGO')===false) $joins = $joins . "INNER JOIN cuentas_mun_gastos ON cuentas_mun_gastos.CODIGO=municipios.CODIGO ";
        }

        if(!empty($choice)){
            if($choice =='SelectYear'){
                if(!empty($anho)){
                    if($conditions!=""){
                        $conditions = $conditions . "AND ";
                    }
                    $conditions = $conditions."scoring_mun.ANHO = '$anho' ";
                }
            }
            else {
                if(!empty($from) && !empty($to)){
                    if($conditions!=""){
                        $conditions = $conditions . "AND ";
                    }
                    $conditions = $conditions."scoring_mun.ANHO BETWEEN '$from' AND '$to' ";
                }
                else if(!empty($from) && empty($to)){
                    
                    if($conditions!=""){
                        $conditions = $conditions . "AND ";
                    }
                    $conditions = $conditions."scoring_mun.ANHO >= '$from' ";
                }
                else if(empty($from) && !empty($to)){
                    
                    if($conditions!=""){
                        $conditions = $conditions . "AND ";
                    }
                    $conditions = $conditions."scoring_mun.ANHO <= '$to' ";
                }
            }
        }

        if(!empty($autonomia)){
            if($conditions!=""){
                $conditions = $conditions . "AND ";
            }
            $conditions = $conditions."municipios.AUTONOMIA = '$autonomia' ";
            $returning_values = $returning_values.",municipios.AUTONOMIA";
        }   
        
        if(!empty($provincia)){
            if($conditions!=""){
                $conditions = $conditions . "AND ";
            }
            $conditions = $conditions."municipios.PROVINCIA = '$provincia' ";
            $returning_values = $returning_values.",municipios.PROVINCIA";
        }

        if($conditions!=""){
            $conditions =" WHERE ".$conditions;
        }
        
        if($having!=""){
            $having =" HAVING ".$having;
        }

        //$sql = "SELECT DEUDAFIN FROM deudas_mun WHERE CODIGO = '$codigo' AND ANHO = '$year'";
        //$sql = "SELECT DISTINCT(municipios.CODIGO), municipios.NOMBRE, scoring_mun.ANHO $returning_values FROM municipios INNER JOIN scoring_mun ON municipios.CODIGO = scoring_mun.CODIGO INNER JOIN deudas_mun ON deudas_mun.CODIGO = municipios.CODIGO $conditions ORDER BY scoring_mun.ANHO DESC, municipios.CODIGO ASC";
        $sql = "SELECT municipios.CODIGO, municipios.NOMBRE, scoring_mun.ANHO $returning_values FROM municipios INNER JOIN scoring_mun ON municipios.CODIGO = scoring_mun.CODIGO $joins $conditions GROUP BY municipios.CODIGO, scoring_mun.ANHO $having ORDER BY scoring_mun.ANHO DESC, municipios.CODIGO ASC";
        //echo $sql;
        //$elements=array();
        $result = mysqli_query($db, $sql);
        if(!$result){
            return false;
        }
        $elements=array();
        while($resultado = mysqli_fetch_assoc($result)){
            $elements2 = array();
            $mun = new Municipio();
            $mun->setNombre($resultado['NOMBRE']);
            if(!empty($resultado['RATING'])) $mun->setScoring($resultado['RATING']);
            if(!empty($resultado['POBLACION']))$mun->setPoblacion($resultado['POBLACION']);
            if(!empty($resultado['R1']))$mun->setEndeudamiento($resultado['R1']);
            if(!empty($resultado['R2']))$mun->setSostenibilidadFinanciera($resultado['R2']);
            if(!empty($resultado['FONDLIQUIDOS']))$mun->setLiquidezInmediata($resultado['FONDLIQUIDOS']);
            if(!empty($resultado['PMP']))$mun->setPeriodoMedioPagos($resultado['PMP']);
            if(!empty($resultado['SUMA_INGR']))$mun->setTotalIngresosNoCorrientes1($resultado['SUMA_INGR']);
            if(!empty($resultado['OBLG'])) {
                if($resultado['TIPO']=='PARTIDAGAST1') $mun->setGastosPersonal1($resultado['OBLG']);
                else if($resultado['TIPO']=='PARTIDAGAST2') $mun->setGastosCorrientesBienesServicios1($resultado['OBLG']);
                else if($resultado['TIPO']=='PARTIDAGAST3') $mun->setTransferenciasCorrientesGastos1($resultado['OBLG']);
                else if($resultado['TIPO']=='PARTIDAGAST6') $mun->setInversionesReales1($resultado['OBLG']);
            }
            if(!empty($resultado['AUTONOMIA'])) {    
                $ccaaCode = $resultado['AUTONOMIA'];
                $sql = "SELECT NOMBRE FROM ccaas WHERE CODIGO = '$ccaaCode'";
                $resultCode = mysqli_query($db,$sql);
                if(!$resultCode){
                    return false;
                }
                $autonomia = mysqli_fetch_assoc($resultCode);
                $mun->setAutonomia($autonomia['NOMBRE']);
            }
            if(!empty($resultado['PROVINCIA'])) $mun->setProvincia(((new DAOConsultorProvincia())->getProvinciaById($resultado['PROVINCIA']))->getNombre());
            $elements2[$resultado['ANHO']]=$mun;
            array_push($elements, $elements2);
        }

        return $elements;
    }  

}


?>