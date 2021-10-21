<?php
if (!isConnect('admin')) {
	throw new Exception('{{Error 401 Unauthorized}}');
}
$plugin = plugin::byId('thermostat');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction logoPrimary" data-action="add">
				<i class="fas fa-plus-circle"></i>
				<br/>
				<span>{{Ajouter}}</span>
			</div>
		</div>
		<legend><i class="fas fa-thermometer-three-quarters"></i> {{Mes thermostats}}</legend>
		<?php
		if (count($eqLogics) == 0) {
			echo '<br/><div class="text-center" style="font-size:1.2em;font-weight:bold;">{{Aucun thermostat trouvé, cliquer sur "Ajouter" pour commencer}}</div>';
		} else {
			echo '<div class="input-group" style="margin:5px;">';
			echo '<input class="form-control roundedLeft" placeholder="{{Rechercher}}" id="in_searchEqlogic"/>';
			echo '<div class="input-group-btn">';
			echo '<a id="bt_resetSearch" class="btn" style="width:30px"><i class="fas fa-times"></i></a>';
			echo '<a class="btn roundedRight hidden" id="bt_pluginDisplayAsTable" data-coreSupport="1" data-state="0"><i class="fas fa-grip-lines"></i></a>';
			echo '</div>';
			echo '</div>';
			echo '<div class="eqLogicThumbnailContainer">';
			foreach ($eqLogics as $eqLogic) {
				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor '.$opacity.'" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $plugin->getPathImgIcon() . '"/>';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
			echo '</div>';
		}
		?>
	</div>

	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-sm btn-default eqLogicAction roundedLeft" data-action="configure"><i class="fas fa-cogs"></i><span class="hidden-xs"> {{Configuration avancée}}</span>
				</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}
				</a><a class="btn btn-sm btn-danger eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}
				</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a class="eqLogicAction cursor" aria-controls="home" role="tab" data-action="returnToThumbnailDisplay"><i class="fas fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Équipement}}</a></li>
			<li role="presentation"><a href="#configureAction" data-toggle="tab"><i class="far fa-hand-paper"></i><span class="hidden-xs"> {{Actions}}</span></a></li>
			<li role="presentation"><a href="#configureMode" data-toggle="tab"><i class="fas fa-th-list"></i><span class="hidden-xs"> {{Modes}}</span></a></li>
			<li role="presentation"><a href="#configureWindows" data-toggle="tab"><i class="icon jeedom-fenetre-ouverte"></i><span class="hidden-xs"> {{Ouvertures}}</span></a></li>
			<li role="presentation"><a href="#configureFailure" data-toggle="tab"><i class="fas fa-exclamation-triangle"></i><span class="hidden-xs"> {{Défaillances}}</span></a></li>
			<?php
			try {
				$pluginCalendar = plugin::byId('calendar');
				if (is_object($pluginCalendar)) {
					?>
					<li  role="presentation"><a href="#configureSchedule" data-toggle="tab"><i class="far fa-clock"></i><span class="hidden-xs"> {{Programmation}}</span></a></li>
					<?php
				}
			} catch (Exception $e) {

			}
			?>
			<li  role="presentation"><a href="#configureAdvanced" data-toggle="tab"><i class="fas fa-cog"></i><span class="hidden-xs"> {{Avancé}}</span></a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-wrench"></i> {{Général}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Nom du thermostat}}</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
									<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du thermostat}}"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label" >{{Objet parent}}</label>
								<div class="col-sm-7">
									<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
										<option value="">{{Aucun}}</option>
										<?php
										$options = '';
										foreach ((jeeObject::buildTree(null, false)) as $object) {
											$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
										}
										echo $options;
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Options}}</label>
								<div class="col-sm-7">
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
									<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
								</div>
							</div>

							<legend><i class="fas fa-cogs"></i> {{Paramètres}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Mode de fonctionnement}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Choisir le mode de fonctionnement du moteur du thermostat}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="engine">
										<option value="temporal">{{Temporel}}</option>
										<option value="hysteresis">{{Hystérésis}}</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Type de thermostat}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Actions que le thermostat est en mesure d'effectuer en terme de chauffage et de refroidissement}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<select class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="allow_mode">
										<option value="heat">{{Chauffage uniquement}}</option>
										<option value="cool">{{Climatisation uniquement}}</option>
										<option value="all">{{Chauffage ET Climatisation}}</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Consigne minimale}} <sub>(°C)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Valeur de la consigne minimale qu'il est possible de transmettre au thermostat}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="order_min" placeholder="15"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Consigne maximale}} <sub>(°C)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Valeur de la consigne maximale qu'il est possible de transmettre au thermostat}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="order_max" placeholder="28"/>
								</div>
							</div>
						</div>

						<div class="col-lg-6">
							<legend><i class="fas fa-project-diagram"></i> {{Sondes}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Température intérieure}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Sélectionner la commande donnant la température de la pièce}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control tooltips roundedLeft" data-l1key="configuration" data-l2key="temperature_indoor" data-concat="1"/>
										<span class="input-group-btn">
											<a class="btn btn-default listCmdInfo roundedRight"><i class="fas fa-list-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Température minimale}} <sub>(°C)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Température en dessous de laquelle une défaillance de chauffage est enclenchée}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_indoor_min" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Température maximale}} <sub>(°C)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Température au dessus de laquelle une défaillance de chauffage est enclenchée}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_indoor_max" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Température extérieure}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Sélectionner la commande donnant la température extérieure (obligatoire en mode Temporel)}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control tooltips roundedLeft" data-l1key="configuration" data-l2key="temperature_outdoor" data-concat="1"/>
										<span class="input-group-btn">
											<a class="btn btn-default listCmdInfo roundedRight"><i class="fas fa-list-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Consommation}} <sub>(kWh/jour)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Sélectionner la commande donnant la consommation du chauffage/climatisation par jour en kilowatt-heure (facultatif)}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control tooltips roundedLeft" data-l1key="configuration" data-l2key="consumption"/>
										<span class="input-group-btn">
											<a class="btn btn-default listCmdInfo roundedRight"><i class="fas fa-list-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Commande personnelle}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Sélectionner une commande de votre choix à afficher sur le thermostat (facultatif)}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control tooltips roundedLeft" data-l1key="configuration" data-l2key="customCmd"/>
										<span class="input-group-btn">
											<a class="btn btn-default listCmdInfo roundedRight"><i class="fas fa-list-alt"></i></a>
										</span>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</form>

			</div>

			<div class="tab-pane" id="configureAction">
				<form class="form-horizontal">
					<fieldset>
						<div>
							<legend>
								<i class="fab fa-hotjar"></i>	{{Pour}} <strong>{{chauffer}}</strong>, {{je dois ?}}
								<sup><i class="fas fa-question-circle tooltips" title="{{Choisir les actions permettant de démarrer le chauffage}}"></i></sup>
								<a class="btn btn-sm btn-danger pull-right addAction" data-type="heat"><i class="fas fa-plus-circle"></i> {{Ajouter une action}}</a>
							</legend>
							<div id="div_heat" class="col-xs-12">
							</div>
						</div>
					</fieldset>
				</form>
				<hr>
				<form class="form-horizontal">
					<fieldset>
						<div>
							<legend>
								<i class="fas fa-icicles"></i> {{Pour}} <strong>{{refroidir}}</strong>, {{je dois ?}}
								<sup><i class="fas fa-question-circle tooltips" title="{{Choisir les actions permettant de démarrer le refroidissement}}"></i></sup>
								<a class="btn btn-sm btn-info pull-right addAction" data-type="cool"><i class="fas fa-plus-circle"></i> {{Ajouter une action}}</a>
							</legend>
							<div id="div_cool" class="col-xs-12">
							</div>
						</div>
					</fieldset>
				</form>
				<hr>
				<form class="form-horizontal">
					<fieldset>
						<div>
							<legend>
								<i class="fas fa-ban"></i> {{Pour}} <strong>{{tout arrêter}}</strong>, {{je dois ?}}
								<sup><i class="fas fa-question-circle tooltips" title="{{Choisir les actions permettant de tout arrêter}}"></i></sup>
								<a class="btn btn-sm btn-warning pull-right addAction" data-type="stop"><i class="fas fa-plus-circle"></i> {{Ajouter une action}}</a>
							</legend>
							<div id="div_stop" class="col-xs-12">
							</div>
						</div>
					</fieldset>
				</form>
				<hr>
				<form class="form-horizontal">
					<fieldset>
						<div>
							<legend>
								<i class="fas fa-sliders-h"></i> {{Sur}} <strong>{{changement de consigne}}</strong>, {{je dois ?}}
								<sup><i class="fas fa-question-circle tooltips" title="{{Choisir les actions à effectuer en cas de changement de la température de consigne du thermostat}}"></i></sup>
								<a class="btn btn-sm btn-default pull-right addAction" data-type="orderChange"><i class="fas fa-plus-circle"></i> {{Ajouter une action}}</a>
							</legend>
							<div id="div_orderChange" class="col-xs-12">
							</div>
						</div>
					</fieldset>
				</form>
				<br>
			</div>

			<div class="tab-pane" id="configureMode">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<div class="alert alert-info col-xs-10 col-xs-offset-1">
							{{Ajouter des modes}} <em>{{(Confort, Eco, Hors-gel, Nuit, Jour, Absence, Vacances, etc...)}}</em> {{et définir les actions qui seront exécutées lors de l'entrée dans chaque mode.}}
							<br>
							<em>{{Exemple : un mode}} <strong>{{Confort}}</strong> {{avec une action sur la commande}} <strong>{{Thermostat}}</strong> {{du plugin permettant de  définir une température de consigne de}} <strong>20</strong> <sup>(°C)</sup></em>
						</div>
						<a class="btn btn-success addMode col-xs-6 col-xs-offset-3"><i class="fas fa-plus-circle"></i> {{Ajouter un mode}}</a>
						<hr>
						<div id="div_modes" class="col-xs-12">
						</div>
					</fieldset>
				</form>
			</div>

			<div class="tab-pane" id="configureWindows">
				<br>
				<form class="form-horizontal">
					<fieldset>
						<div class="alert alert-info col-xs-10 col-xs-offset-1">
							{{Déclarer les ouvrants concernés par ce thermostat}} <em>{{(portes, fenêtres, etc...)}}</em> {{permettra de réguler la température en fonction de leur état.}}
						</div>
						<a class="btn btn-success addWindow col-xs-6 col-xs-offset-3" data-type="window"><i class="fas fa-plus-circle"></i> {{Ajouter un ouvrant}}</a>
						<legend class="col-xs-12">
							<i class="fas fa-exclamation-circle"></i> {{Alerte si ouverture de plus de}}
							<input type="number" class="eqLogicAttr tooltips" data-l1key="configuration" data-l2key="window_alertIfOpenMoreThan" style="width:50px;"/>
							{{minutes}}
							<sup><i class="fas fa-question-circle tooltips" title="{{Envoyer une alerte si un ouvrant défini ici reste ouvert durant plus de XX minutes}}"></i></sup>
							<br>
						</legend>
						<div id="div_window" class="col-xs-12">

						</div>
					</fieldset>
				</form>
			</div>

			<div class="tab-pane" id="configureFailure">
				<br>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fas fa-thermometer-empty"></i> {{Défaillance sonde}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-info col-xs-10 col-xs-offset-1">
									{{Décider des actions à entreprendre en cas de défaillance de la sonde de température.}}
									<br>{{Une sonde est considérée défaillante selon le délai entre 2 réceptions de température défini dans l'onglet}} <strong>{{Avancé (Délai maximal entre 2 changements de température)}}</strong>.
								</div>
								<a class="btn btn-success addFailure col-xs-6 col-xs-offset-3" data-type="failure"><i class="fas fa-plus-circle"></i> {{Ajouter une action de défaillance}}</a>
								<div id="div_failure"></div>
							</fieldset>
						</form>
						<br>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="icon techno-heating3"></i> {{Défaillance chauffage}}</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal">
							<fieldset>
								<div class="alert alert-info col-xs-10 col-xs-offset-1">
									{{Décider des actions à entreprendre en cas de défaillance du chauffage/climatisation.}}
									<br>{{Le système de chauffage/climatisation est considéré défaillant quand les températures minimales ou maximales définies sur l'onglet}} <strong>{{Équipement}}</strong> {{sont dépassées ou en fonction des marges de défaillance définies dans l'onglet}} <strong>{{Avancé}}</strong>.
								</div>
								<a class="btn btn-success addFailureActuator col-xs-6 col-xs-offset-3" data-type="failureActuator"><i class="fas fa-plus-circle"></i> {{Ajouter une action de défaillance}}</a>
								<div id="div_failureActuator"></div>
							</fieldset>
						</form>
						<br>
					</div>
				</div>
			</div>

			<div class="tab-pane" id="configureSchedule">
				<form class="form-horizontal">
					<fieldset>
						<br/>
						<div id="div_schedule"></div>
					</fieldset>
				</form>
			</div>

			<div class="tab-pane" id="configureAdvanced">
				<form class="form-horizontal">
					<fieldset>
						<div class="col-lg-6">
							<legend><i class="fas fa-shield-alt"></i> {{Sécurités}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Cron de répétition}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Cron de répétition d'envoi des commandes (arrêt, chauffe, refroidissement). Vérification à mettre en place si votre thermostat ne démarre/s'arrête pas correctement}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="repeat_commande_cron"/>
										<span class="input-group-btn">
											<a class="btn btn-default btn-sm cursor jeeHelper" data-helper="cron"><i class="fas fa-question-circle"></i></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group engine hysteresis">
								<label class="col-sm-4 control-label">{{Cron de contrôle}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Cron de vérification des valeurs des sondes de température à mettre en place si votre thermostat ne démarre/s'arrête pas correctement}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<div class="input-group">
										<input type="text" class="eqLogicAttr form-control tooltips jeeHelper" data-helper="cron" data-l1key="configuration" data-l2key="hysteresis_cron"/>
										<span class="input-group-btn">
											<a class="btn btn-default btn-sm cursor jeeHelper" data-helper="cron"><i class="fas fa-question-circle"></i></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Délai entre 2 changements de T°}} <sub>(min.)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Délai maximal entre 2 changements de température avant de mettre le thermostat en défaillance}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="maxTimeUpdateTemp"/>
								</div>
							</div>
							<div class="form-group engine temporal">
								<label class="col-sm-4 control-label">{{Offset chauffage}} <sub>(%)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Permet d'adapter la chauffe en fonction des apports internes}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offset_heat" placeholder="0"/>
								</div>
							</div>
							<div class="form-group engine temporal">
								<label class="col-sm-4 control-label">{{Offset refroidissement}} <sub>(%)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Permet d'adapter le refroidissement en fonction des apports internes}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offset_cool" placeholder="0"/>
								</div>
							</div>
							<div class="form-group engine temporal">
								<label class="col-sm-4 control-label">{{Marge de défaillance chaud}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Déclenchement de la défaillance chaud par rapport à l'écart entre la température et la consigne pendant 3 cycles d'affilées (1 par défaut)}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offsetHeatFaillure" value="1" placeholder="1"/>
								</div>
							</div>
							<div class="form-group engine temporal">
								<label class="col-sm-4 control-label">{{Marge de défaillance froid}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Déclenchement de la défaillance froid par rapport à l'écart entre la température et la consigne pendant 3 cycles d'affilées (1 par défaut)}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offsetColdFaillure" value="1" placeholder="1"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Masquer commande de verrouillage}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Cocher la case pour ne pas afficher la commande de verrouillage sur le widget}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="hideLockCmd" />
								</div>
							</div>
						</div>


						<div class="col-lg-6 engine temporal">
							<legend><i class="fas fa-sync-alt"></i> {{Cycles}}</legend>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Durée du cycle}} <sub>(min.)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Durée des cycles de chauffe/climatisation (ne peut pas être inférieure à 15 minutes)}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="cycle" placeholder="60"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Temps de chauffe minimal}} <sub>(% {{cycle}})</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Pourcentage minimal de chauffe durant la durée du cycle}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="minCycleDuration" value="5" placeholder="5"/>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Le radiateur est considéré chaud}} <sub>(% {{cycle}})</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Pourcentage du cycle après lequel le radiateur est considéré comme étant chaud}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="threshold_heathot" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Offset si le radiateur est chaud}} <sub>(%)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Offset à appliquer sur les cycles suivants lorsque le radiateur est considéré chaud}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="number" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="offset_nextFullCyle" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">{{Limiter les cycles marche/arrêt et PID}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Permet de maintenir la chauffe sur le cycle suivant en cas de cycle court (pellets, gaz, fioul)}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="stove_boiler" />
								</div>
							</div>
							<br>
						</div>

						<div class="col-lg-6 engine hysteresis">
							<legend><i class="fas fa-poll-h"></i> {{Régulation}}</legend>
							<div class="form-group">
								<label class="col-sm-3 control-label">{{Valeur d'hystérésis}} <sub>(°C)</sub>
									<sup><i class="fas fa-question-circle tooltips" title="{{Correspond à l'écart entre la température intérieure et la consigne pour démarrer/arrêter le thermostat}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="hysteresis_threshold" placeholder="1"/>
								</div>
							</div>
							<div class="form-group positiveHysteresis">
								<label class="col-sm-3 control-label">{{Hystérésis positive}}
									<sup><i class="fas fa-question-circle tooltips" title="{{Cocher la case pour que seule l'hystérésis positive soit prise en compte pour la chauffe ou le refroidissement}}"></i></sup>
								</label>
								<div class="col-sm-7">
									<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="positiveHysteresis" />
								</div>
							</div>

						</div>
						<div class="col-xs-12 engine temporal">
							<legend><i class="fas fa-poll-h"></i> {{Régulation}}</legend>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Delta <small>(consigne - T° extérieure)</small> chaud}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Permet de changer la direction en fonction du rapport entre la consigne et la température extérieure}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="direction::delta::heat" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Delta <small>(consigne - T° extérieure)</small> froid}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Permet de changer la direction en fonction du rapport entre la consigne et la température extérieure)}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="direction::delta::cool" />
									</div>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Auto-apprentissage}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Décocher la case pour désactiver l'auto-apprentissage (les coefficients ci-dessous devront donc être renseignés manuellement)}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="autolearn" checked />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Smart-start}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Autoriser le thermostat à démarrer avant l’heure afin que la température atteigne la consigne à l’heure voulue. Ne fonctionne que si le thermostat est géré par le plugin agenda}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="checkbox" class="eqLogicAttr tooltips" data-l1key="configuration" data-l2key="smart_start" checked />
									</div>
								</div>
							</div>

							<br>
							<div class="alert alert-warning col-xs-10 col-xs-offset-1">
								<i class="fas fa-exclamation-triangle"></i>
								{{Pour une meilleure régulation il est conseillé de ne pas toucher à ces coefficients car ils sont calculés et mis à jour automatiquement au fur et à mesure de l'auto-apprentissage.}}
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Coefficient chauffage}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Cette valeur est multipliée par l’écart entre la consigne et la température intérieure pour déduire le temps de chauffage}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_heat" placeholder="10"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Apprentissage chaud}}
										<sup><i class="fas fa-question-circle tooltips" title="{{État d’avancement de l’apprentissage chaud (50 indique la fin de l’apprentissage)}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_heat_autolearn" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Isolation chauffage}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Cette valeur est multipliée par l’écart entre la consigne et la température extérieure pour déduire le temps de chauffage}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_heat" placeholder="2"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Apprentissage isolation chaud}}
										<sup><i class="fas fa-question-circle tooltips" title="{{État d’avancement de l’apprentissage de l'isolation chaud (50 indique la fin de l’apprentissage)}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_heat_autolearn" />
									</div>
								</div>
							</div>

							<div class="col-lg-6">
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Coefficient refroidissement}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Cette valeur est multipliée par l’écart entre la consigne et la température intérieure pour déduire le temps de refroidissement}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_cool" placeholder="10"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Apprentissage froid}}
										<sup><i class="fas fa-question-circle tooltips" title="{{État d’avancement de l’apprentissage froid (50 indique la fin de l’apprentissage)}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_cool_autolearn" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Isolation refroidissement}}
										<sup><i class="fas fa-question-circle tooltips" title="{{Cette valeur est multipliée par l’écart entre la consigne et la température extérieure pour déduire le temps de refroidissement}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_cool" placeholder="2"/>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">{{Apprentissage isolation froid}}
										<sup><i class="fas fa-question-circle tooltips" title="{{État d’avancement de l’apprentissage de l'isolation froid (50 indique la fin de l’apprentissage)}}"></i></sup>
									</label>
									<div class="col-sm-7">
										<input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_cool_autolearn" />
									</div>
								</div>
							</div>
							<a class="col-xs-6 col-xs-offset-3 btn btn-default tooltips" id="bt_razLearning" title="{{Réinitialise le processus d'apprentissage. N'oubliez pas de sauvegarder après la remise à 0.}}"><i class="fas fa-times"></i> RaZ apprentissage</a>

						</div>
					</fieldset>
				</form>
			</div>

		</div>
	</div>
</div>

<?php include_file('desktop', 'thermostat', 'js', 'thermostat');?>
<?php include_file('core', 'plugin.template', 'js');?>
