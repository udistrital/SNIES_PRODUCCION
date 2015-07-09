<?php

namespace component\GestorSniesLocal;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	function cadena_sql($tipo, $variable = "") {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = 'sniesud_';
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			// CONSULTAS BASE DE DATOS SNIES LOCAL - POSTGRES
			
			case "contarInscritos" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " inscrito ";
				$cadenaSql .= " WHERE ins_annio=" . $variable ['annio'];
				$cadenaSql .= " AND ins_semestre='" . $variable ['semestre'] . "'";
				
				break;
			
			case "contarAdmitidos" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " admitido ";
				$cadenaSql .= " WHERE adm_annio=" . $variable ['annio'];
				$cadenaSql .= " AND adm_semestre='" . $variable ['semestre'] . "'";
				
				break;
			
			case "contarMatriculadosPrimerCurso" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " estudiante_programa ";
				$cadenaSql .= " WHERE anio=" . $variable ['annio'];
				$cadenaSql .= " AND semestre='" . $variable ['semestre'] . "'";
				
				break;
			
			case "contarMatriculados" :
				$cadenaSql = "SELECT COUNT(*) FROM";
				$cadenaSql .= " matriculado ";
				$cadenaSql .= " WHERE est_annio=" . $variable ['annio'];
				$cadenaSql .= " AND est_semestre='" . $variable ['semestre'] . "'";
				
				break;
			
			// CONSULTAS BASE DE DATOS ACADEMICA - ORACLE
			
			case "consultarInscritoAcademica" :
				$prefijo = "mntac.";
				$cadenaSql = "SELECT UNIQUE ";
				$cadenaSql .= "TO_CHAR('1301') ies_code, ";
				$cadenaSql .= "asp_ape_ano ins_annio, ";
				$cadenaSql .= "DECODE(asp_ape_per,1,'01',3,'02', asp_ape_per) ins_per, ";
				$cadenaSql .= "DECODE(asp_tip_doc,'',DECODE(length(asp_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_ident_code, ";
				$cadenaSql .= "asp_nro_iden documento, ";
				$cadenaSql .= "case
                                        when INSTR(trim(asp_apellido),' ',1,1)='0'
                                        then ' '
                                        else SUBSTR(trim(asp_apellido),instr(trim(asp_apellido),' ',-1,1),length(trim(asp_apellido)))
                                        end segundo_apellido, ";
				$cadenaSql .= "as_cra_cod_snies prog_prim_opcion, ";
				$cadenaSql .= "TO_CHAR(DECODE(asp_snp,'','N/A',NULL,'N/A',replace(asp_snp,' ',''))) snp,";
				$cadenaSql .= "TO_CHAR(DECODE(asp_sexo,'M','01','F','02','01')) genero, ";
				$cadenaSql .= "case
                                        when INSTR(trim(asp_nombre),' ',1,1)='0'
                                        then SUBSTR(trim(asp_nombre),instr(trim(asp_nombre),' ',1,1),length(trim(asp_nombre)))
                                        else SUBSTR(trim(asp_nombre),0,INSTR(trim(asp_nombre),' ',1,1))
                                        end primer_nombre, ";
				$cadenaSql .= "case
                                        when INSTR(trim(asp_nombre),' ',1,1)='0'
                                        then ' '
                                        else SUBSTR(trim(asp_nombre),instr(trim(asp_nombre),' ',-1,1),length(trim(asp_nombre)))
                                        end segundo_nombre, ";
				$cadenaSql .= "case
                                        when INSTR(trim(asp_apellido),' ',1,1)='0'
                                        then SUBSTR(trim(asp_apellido),instr(trim(asp_apellido),' ',1,1),length(trim(asp_apellido)))
                                        else SUBSTR(trim(asp_apellido),0,INSTR(trim(asp_apellido),' ',1,1))
                                        end primer_apellido, ";
				$cadenaSql .= "'1301' codigo_ent_aula, ";
				$cadenaSql .= "'11001' municipio, ";
				$cadenaSql .= "'11' departamento, ";
				$cadenaSql .= "as_cra_nom prog ";
				$cadenaSql .= "FROM " . $prefijo . "accra_snies ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "accra ON cra_cod = as_cra_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "acasp ON cra_cod = asp_cra_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "actipcra ON cra_tip_cra = tra_cod ";
				$cadenaSql .= "WHERE  ";
				$cadenaSql .= "asp_ape_ano=" . $variable ['annio'];
				$cadenaSql .= "AND asp_ape_per='" . $variable ['semestre'] . "'";
				$cadenaSql .= "AND tra_nivel IN ('PREGRADO') ";
				
				$cadenaSql .= "UNION ";
				
				$cadenaSql .= "SELECT UNIQUE ";
				$cadenaSql .= "TO_CHAR('1301') ies_code, ";
				$cadenaSql .= "mat_ano ins_annio, ";
				$cadenaSql .= "DECODE(mat_per,1,'01',3,'02', mat_per) ins_per, ";
				$cadenaSql .= "DECODE(est_tipo_iden,'',DECODE(length(est_nro_iden),11,'TI',12,'TI','CC'),'C', 'CC', '1', 'CC', 'c', 'CC', 'T', 'TI', '2', 'TI', 't', 'TI', 'E', 'CE', 'P', 'PS', 'CC') tipo_ident_code, ";
				$cadenaSql .= "est_nro_iden  documento, ";
				$cadenaSql .= "(case when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,1)+1,INSTR(trim(est_nombre),' ',1,3) - INSTR(trim(est_nombre),' ',1,2))) 
                                      when INSTR(trim(est_nombre),' ',1,4)='0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,1)+1,length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,3)))
                                      else trim(SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,1) +1 ,INSTR(trim(est_nombre),' ',1,2) - INSTR(trim(est_nombre),' ',1,1)))
                                      end) segundo_apellido, ";
				$cadenaSql .= "as_cra_cod_snies prog_prim_opcion,  ";
				$cadenaSql .= "TO_CHAR(DECODE(eot_nro_snp,'','N/A',NULL,'N/A',replace(eot_nro_snp,' ',''))) snp,";
				$cadenaSql .= "TO_CHAR(DECODE(est_sexo,'M','01','F','02','01')) genero, ";
				$cadenaSql .= "(case when INSTR(trim(est_nombre),' ',1,3)='0' AND INSTR(trim(est_nombre),' ',1,2)='0' 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,1) +1,length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,1))) 
                                      when INSTR(trim(est_nombre),' ',1,3)='0' AND INSTR(trim(est_nombre),' ',1,2)>'0' 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,2) +1,length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,2))) 
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,3),INSTR(trim(est_nombre),' ',1,4) - INSTR(trim(est_nombre),' ',1,3))) 
                                      when INSTR(trim(est_nombre),' ',1,4)='0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,3),length(trim(est_nombre)) - instr(trim(est_nombre),' ',1,3)))
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,2)+1,INSTR(trim(est_nombre),' ',1,4) - INSTR(trim(est_nombre),' ',1,3))) 
                                      when INSTR(trim(est_nombre),' ',1,4)='0' AND INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,3),length(est_nombre) - instr(est_nombre,' ',1,3)+1))
                                      else trim(SUBSTR(trim(est_nombre),INSTR(trim(est_nombre),' ',1,2)+1 ,INSTR(trim(est_nombre),' ',1,3) - INSTR(trim(est_nombre),' ',1,2))) 
                                      end) primer_nombre, ";
				$cadenaSql .= "(case when INSTR(trim(est_nombre),' ',1,3)='0'
                                      then ' ' 
                                      when INSTR(trim(est_nombre),' ',1,4)='0' AND (INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 OR INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1)
                                      then ' '
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,2)+1,INSTR(trim(est_nombre),' ',1,4) - INSTR(trim(est_nombre),' ',1,3))) 
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,2)=INSTR(trim(est_nombre),' ',1,1)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,4),(length(trim(est_nombre))+1) - INSTR(trim(est_nombre),' ',1,4))) 
                                      when INSTR(trim(est_nombre),' ',1,4)>'0' AND INSTR(trim(est_nombre),' ',1,3)=INSTR(trim(est_nombre),' ',1,2)+1 
                                      then trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,4),(length(trim(est_nombre))+1) - instr(est_nombre,' ',1,4)+1))
                                      else trim(SUBSTR(trim(est_nombre),instr(trim(est_nombre),' ',1,3) +1,(length(est_nombre)+1) - instr(est_nombre,' ',1,3))) 
                                      end) segundo_nombre,  ";
				$cadenaSql .= "SUBSTR(trim(est_nombre),0,INSTR(trim(est_nombre),' ',1,1)) primer_apellido, ";
				$cadenaSql .= "'1301' codigo_ent_aula, ";
				$cadenaSql .= "'11001' municipio, ";
				$cadenaSql .= "'11' departamento, ";
				$cadenaSql .= "as_cra_nom prog ";
				$cadenaSql .= "FROM " . $prefijo . "acest ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "acestotr ON est_cod = eot_cod  ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "v_tot_matri_ape_per ON est_cod = mat_est_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "accra ON cra_cod = mat_cra_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "actipcra ON cra_tip_cra = tra_cod ";
				$cadenaSql .= "INNER JOIN " . $prefijo . "accra_snies ON as_cra_cod = mat_cra_cod ";
				$cadenaSql .= "WHERE  ";
				$cadenaSql .= "mat_ano=" . $variable ['annio'];
				$cadenaSql .= "AND mat_per='" . $variable ['semestre'] . "'";
				$cadenaSql .= "AND SUBSTR(est_cod,0,4)=mat_ano ";
				$cadenaSql .= "AND SUBSTR(est_cod,5,1)=DECODE(mat_per,1,'1',3,'2',mat_per) ";
				$cadenaSql .= "AND tra_nivel IN ('DOCTORADO','MAESTRIA','POSGRADO') ";
				
				break;
		}
		
		return $cadenaSql;
	}
}
?>
