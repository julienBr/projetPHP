<?php
	$idr=$_GET['idr'];
	if(isset($idr)) {
		try {
			$req=$PDO_BDD->prepare('select R.RCT_ID, RCT_TITRE, UTI_LOGIN, RCT_ILLUSTRATION,
					((HOUR(RCT_TEMPS_PREPARATION) * 60) + MINUTE(RCT_TEMPS_PREPARATION)) as RCT_TEMPS_PREPARATION,
					((HOUR(RCT_TEMPS_CUISSON) * 60) + MINUTE(RCT_TEMPS_CUISSON)) as RCT_TEMPS_CUISSON, RCT_COUT,
					RCT_DIFFICULTE, RCT_NBPERSONNE, RCT_PREPARATION,IGD_RCT_UNI_QUANTITE, UNI_SHORT_LABEL, IGD_LABEL
									from tj_igd_rct_uni as RIU join t_recette_rct as R on RIU.RCT_ID = R.RCT_ID
															   join t_utilisateur_uti as U on R.UTI_ID = U.UTI_ID
															   join t_unite_uni as UN on RIU.UNI_ID = UN.UNI_ID
															   join t_ingredient_igd as I on RIU.IGD_ID = I.IGD_ID
									where R.RCT_ID = '.$idr.' and R.RCT_STATUT = 3');
			$req->execute();
		} catch (Exception $e) {
			echo 'Erreur : '.$e -> getMessage().'</br>';
			echo 'N° : '.$e -> getCode();
		}
		$i=0;
		while($lignes=$req->fetch(PDO::FETCH_NUM)) {
			$data['RCT_ID'] = $lignes[0];
			$data['RCT_TITRE'] = $lignes[1];
			$data['UTI_LOGIN'] = $lignes[2];
			$data['RCT_ILLUSTRATION'] = $lignes[3];
			$data['RCT_TEMPS_PREPARATION'] = $lignes[4];
			$data['RCT_TEMPS_CUISSON'] = $lignes[5];
			$data['RCT_COUT'] = $lignes[6];
			$data['RCT_DIFFICULTE'] = $lignes[7];
			$data['RCT_NBPERSONNE'] = $lignes[8];
			$data['RCT_PREPARATION'] = nl2br($lignes[9]);
			$data['IGD_RCT_UNI_QUANTITE'][$i] = $lignes[10];
			$data['UNI_SHORT_LABEL'][$i] = $lignes[11];
			$data['IGD_LABEL'][$i] = $lignes[12];
			$i++;
		}
		try {
			$data['nbRecette']=$PDO_BDD->query('select count(RCT_ID)
												from tj_cat_rct
												group by CAT_ID')->fetch()[0];
		}catch (Exception $e) {
			echo 'Erreur : '.$e -> getMessage().'</br>';
			echo 'N° : '.$e -> getCode();
		}
		
		try {
			$req=$PDO_BDD->prepare('select RCT_TITRE, IGD_LABEL, IGD_RCT_UNI_QUANTITE
									from tj_igd_rct_uni as RIU join t_recette_rct as R on RIU.RCT_ID = R.RCT_ID
															   join t_ingredient_igd as I on I.IGD_ID = RIU.IGD_ID
									where RIU.RCT_ID = '.$idr);
			$req->execute();
		} catch (Exception $e) {
			echo 'Erreur : '.$e -> getMessage().'</br>';
			echo 'N° : '.$e -> getCode();
		}
		$i=0;
		while($lignes=$req->fetch(PDO::FETCH_NUM)) {
			$data['IGD_RCT_UNI_QUANTITE'][$i]   = $lignes[2];
			$i++;
		}
		
		try {
			$data['nbIngredient']=$PDO_BDD->query('select count(IGD_LABEL)
												   from tj_igd_rct_uni as RIU join t_recette_rct as R on RIU.RCT_ID = R.RCT_ID
																			  join t_ingredient_igd as I on I.IGD_ID = RIU.IGD_ID
												   where RIU.RCT_ID = '.$idr)->fetch()[0];
		}catch (Exception $e) {
			echo 'Erreur : '.$e -> getMessage().'</br>';
			echo 'N° : '.$e -> getCode();
		}
		
		$smarty->assign("data", $data);
	}