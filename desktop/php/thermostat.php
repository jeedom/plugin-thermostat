<?php
if (!isConnect('admin')) {
	throw new Exception('{{Error 401 Unauthorized}}');
}
$plugin = plugin::byId('thermostat');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>
<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un thermostat}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '" style="' . $opacity . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
}
?>
           </ul>
       </div>
   </div>

   <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
    <legend>{{Gestion}}</legend>
    <div class="eqLogicThumbnailContainer">
        <div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
         <center>
            <i class="fa fa-plus-circle" style="font-size : 5em;color:#94ca02;"></i>
        </center>
        <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02"><center>Ajouter</center></span>
    </div>
</div>
<legend>{{Mes thermostats}}</legend>
<input class="form-control" placeholder="{{Rechercher}}" style="margin-bottom:4px;" id="in_searchEqlogic" />
<div class="eqLogicThumbnailContainer">
    <?php
foreach ($eqLogics as $eqLogic) {
	$opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
	echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;' . $opacity . '" >';
	echo "<center>";
	echo '<img src="' . $plugin->getPathImgIcon() . '" height="105" width="95" />';
	echo "</center>";
	echo '<span class="name" style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
	echo '</div>';
}
?>
</div>
</div>

<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">

 <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
 <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
 <a class="btn btn-default eqLogicAction pull-right" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a>
 <ul class="nav nav-tabs" role="tablist">
   <li role="presentation"><a class="eqLogicAction cursor" aria-controls="home" role="tab" data-action="returnToThumbnailDisplay" style="padding:10px 5px !important"><i class="fa fa-arrow-circle-left"></i></a></li>
   <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab" style="padding:10px 5px !important"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
   <li role="presentation"><a href="#configureAction" data-toggle="tab" style="padding:10px 5px !important"><i class="fa fa-hand-paper-o" aria-hidden="true"></i> {{Actions}}</a></li>
   <li role="presentation"><a href="#configureMode" data-toggle="tab" style="padding:10px 5px !important"><i class="fa fa-modx" aria-hidden="true"></i> {{Modes}}</a></li>
   <li role="presentation"><a href="#configureWindows" data-toggle="tab" style="padding:10px 5px !important"><i class="icon jeedom-fenetre-ouverte"></i> {{Ouvertures}}</a></li>
   <li role="presentation"><a href="#configureFailure" data-toggle="tab" style="padding:10px 5px !important"><i class="fa fa-thermometer-empty" aria-hidden="true"></i> {{Défaillance sonde}}</a></li>
   <li role="presentation"><a href="#configureFailureActuator" data-toggle="tab" style="padding:10px 5px !important"><i class="icon techno-heating3"></i>  {{Défaillance chauffage}}</a></li>
   <?php
try {
	$plugin = plugin::byId('calendar');
	if (is_object($plugin)) {
		?>
          <li  role="presentation"><a href="#configureSchedule" data-toggle="tab" style="padding:10px 5px !important"><i class="fa fa-clock-o" aria-hidden="true"></i> {{Programmation}}</a></li>
          <?php
}
} catch (Exception $e) {

}
?>
  <li  role="presentation"><a href="#configureAdvanced" data-toggle="tab" style="padding:10px 5px !important"><i class="fa fa-cog" aria-hidden="true"></i> {{Avancée}}</a></li>
</ul>
<div class="tab-content">
    <div class="tab-pane active" id="eqlogictab">
        <br/>
        <legend><i class="fa fa-tachometer"></i> {{Général}}</legend>
        <div class="row">
            <div class="col-sm-6">
                <form class="form-horizontal">
                    <fieldset>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Nom du thermostat}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                                <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom du thermostat}}"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" >{{Objet parent}}</label>
                            <div class="col-sm-6">
                                <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                    <option value="">{{Aucun}}</option>
                                    <?php
foreach (object::all() as $object) {
	echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
}
?>
                               </select>
                           </div>
                       </div>
                       <div class="form-group">
                        <label class="col-sm-4 control-label">{{Activer}}</label>
                        <div class="col-sm-8">
                            <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                            <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                        </div>
                    </div>

                </fieldset>
            </form>
        </div>
        <div class="col-sm-6">
            <form class="form-horizontal">
                <fieldset>
                   <div class="form-group">
                    <label class="col-sm-3 control-label">{{Moteur}}</label>
                    <div class="col-sm-6">
                        <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="engine" placeholder="" >
                            <option value="temporal">Temporel</option>
                            <option value="hysteresis">Hysteresis</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{Autoriser}}</label>
                    <div class="col-sm-6">
                        <select class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="allow_mode" title="{{Veuillez préciser les actions que le thermostat a le droit de faire en terme de chauffage et refroidissement.}}">
                            <option value="all">Tout</option>
                            <option value="heat">Chauffage uniquement</option>
                            <option value="cool">Climatisation uniquement</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{Température min (°C)}}</label>
                    <div class="col-sm-2">
                        <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="order_min" title="{{Précisez l'écart de température que le thermostat est autorisé à piloter}}"/>
                    </div>
                    <label class="col-sm-2 control-label">{{max (°C)}}</label>
                    <div class="col-sm-2">
                        <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="order_max" title="{{Précisez l'écart de température que le thermostat est autorisé à piloter}}"/>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<form class="form-horizontal">
    <fieldset>
        <legend><i class="fa fa-thermometer-empty" aria-hidden="true"></i> {{Sonde de température}}</legend>
        <div class="form-group">
            <label class="col-sm-2 control-label">{{Température intérieure}}</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_indoor" data-concat="1"/>
                    <span class="input-group-btn">
                        <a class="btn btn-default listCmdInfo"><i class="fa fa-list-alt"></i></a>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">{{Borne de température inférieure}}</label>
            <div class="col-sm-2">
                <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_indoor_min" />
            </div>
            <label class="col-sm-2 control-label">{{Borne de température supérieure}}</label>
            <div class="col-sm-2">
                <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_indoor_max" />
            </div>
        </div>
        <div class="form-group engine temporal">
            <label class="col-sm-2 control-label">{{Température extérieure}}</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_outdoor" data-concat="1"/>
                    <span class="input-group-btn">
                        <a class="btn btn-default listCmdInfo"><i class="fa fa-list-alt"></i></a>
                    </span>
                </div>
            </div>
        </fieldset>
    </form>
</div>


<div class="tab-pane" id="configureAction">
    <br/>
    <form class="form-horizontal">
        <fieldset>
            <legend>
                {{Pour chauffer je dois ?}}
                <a class="btn btn-danger btn-xs pull-right addAction" data-type="heat" style="position: relative; top : 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter une action}}</a>
            </legend>
            <div id="div_heat">

            </div>
        </fieldset>
    </form>

    <form class="form-horizontal">
        <fieldset>
            <legend>
                {{Pour refroidir je dois ?}}
                <a class="btn btn-primary btn-xs pull-right addAction" data-type="cool" style="position: relative; top : 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter une action}}</a>
            </legend>
            <div id="div_cool">

            </div>
        </fieldset>
    </form>

    <form class="form-horizontal">
        <fieldset>
            <legend>
                {{Pour tout arrêter je dois ?}}
                <a class="btn btn-default btn-xs pull-right addAction" data-type="stop" style="position: relative; top : 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter une action}}</a>
            </legend>
            <div id="div_stop">

            </div>
        </fieldset>
    </form>
    <form class="form-horizontal">
        <fieldset>
            <legend>
                {{A chaque changement de consigne je dois aussi faire ?}}
                <a class="btn btn-default btn-xs pull-right addAction" data-type="orderChange" style="position: relative; top : 5px;"><i class="fa fa-plus-circle"></i> {{Ajouter une action}}</a>
            </legend>
            <div id="div_orderChange">

            </div>
        </fieldset>
    </form>
</div>
<div class="tab-pane" id="configureMode">
    <form class="form-horizontal">
        <fieldset>
            <br/>
            <div class="alert alert-info">
                {{Avec les modes, vous pouvez rajouter à votre thermostat des consignes prédéfinies. Par exemple un mode confort qui déclenche une action sur votre thermostat avec une température de consigne de 20°C}}
                <a class="btn btn-success addMode pull-right" style="position: relative;top: -7px;"><i class="fa fa-plus-circle"></i> Ajouter mode</a>
            </div>
            <br/><br/>
            <div id="div_modes"></div>
        </fieldset>
    </form>
</div>
<div class="tab-pane" id="configureWindows">
    <form class="form-horizontal">
        <fieldset>
            <br/>
            <div class="alert alert-info">
                {{La déclaration des ouvertures concernées par votre thermostat (porte, fenêtre...) permettra au thermostat de réguler la température en conséquence.}}
                <a class="btn btn-success addWindow pull-right" data-type="window" style="position: relative;top: -7px;"><i class="fa fa-plus-circle"></i> {{Ajouter ouverture}}</a>
            </div>
            <br/><br/>
            <div id="div_window"></div>
        </fieldset>
    </form>
</div>
<div class="tab-pane" id="configureFailure">
    <form class="form-horizontal">
        <fieldset>
            <br/>
            <a class="btn btn-success addFailure pull-right" data-type="failure" style="position: relative;top: -7px;"><i class="fa fa-plus-circle"></i> {{Ajouter action de défaillance}}</a>
            <br/><br/>
            <div id="div_failure"></div>
        </fieldset>
    </form>
</div>
<div class="tab-pane" id="configureFailureActuator">
    <form class="form-horizontal">
        <fieldset>
            <br/>
            <a class="btn btn-success addFailureActuator pull-right" data-type="failureActuator" style="position: relative;top: -7px;"><i class="fa fa-plus-circle"></i> {{Ajouter action de défaillance}}</a>
            <br/><br/>
            <div id="div_failureActuator"></div>
        </fieldset>
    </form>
</div>
<div class="tab-pane" id="configureAdvanced">
    <form class="form-horizontal">
        <fieldset>
            <br/><br/>
            <div class="form-group">
                <label class="col-sm-2 control-label">{{Cron de répétition de commande}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="repeat_commande_cron" title="{{Cron de renvoi des commandes du thermostat (arrêt, chauffe, refroidissement), si votre thermostat ne démarre ou ne s'arrête pas correctement mettez en place cette vérification}}"/>
                </div>
                <label class="col-sm-2 control-label">{{Délai max entre 2 relevés de température (min)}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="maxTimeUpdateTemp" title="{{Délai maximum entre 2 relévés de température avant de mettre le thermostat en défaillance}}"/>
                </div>
                <label class="col-sm-2 control-label">{{Masquer commande de verrouillage}}</label>
                <div class="col-sm-2">
                    <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="hideLockCmd" />
                </div>
            </div>
            <div class="form-group engine temporal">
                <label class="col-sm-2 control-label">{{Cycle (min)}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="cycle" title="{{Durée des cycles de chauffe/climatisation (ne peut être inferieure à 15 min)}}"/>
                </div>
                <label class="col-sm-2 control-label">{{Temps de chauffe minimum (% du cycle)}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="minCycleDuration" title="{{% minimum de cycle à faire (sinon la mise en marche du chauffage est reportée au cyle suivant)}}" value="5"/>
                </div>
            </div>
            <div class="form-group engine temporal">
                <label class="col-sm-2 control-label">{{Marge de défaillance chaud}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offsetHeatFaillure" title="{{Seuil de déclenchement de la défaillance chaud (1 par défaut)}}" value="1"/>
                </div>
                <label class="col-sm-2 control-label">{{Marge de défaillance froid}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offsetColdFaillure" title="{{Seuil de déclenchement de la défaillance froid (1 par défaut)}}" value="1"/>
                </div>
            </div>
            <div class="form-group engine temporal">
                <label class="col-sm-2 control-label">{{Offset chauffage (%)}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offset_heat" />
                </div>
                <label class="col-sm-2 control-label">{{Offset Clim (%)}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offset_cool" />
                </div>
            </div>
            <div class="form-group engine temporal">
                <label class="col-sm-2 control-label">{{Auto-apprentissage}}</label>
                <div class="col-sm-2">
                    <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="autolearn" checked />
                </div>
                <label class="col-sm-2 control-label">{{Smart start}}</label>
                <div class="col-sm-2">
                    <input type="checkbox" class="eqLogicAttr tooltips" data-l1key="configuration" data-l2key="smart_start" checked title="{{Autorise le thermostat à partir avant pour que la température soit égale à la consigne à l'heure voulue. Attention ne marche que si le thermostat est géré par le plugin agenda.}}" />
                </div>
            </div>
            <div class="alert alert-warning">
                {{Pour une meilleure régulation, il est conseillé de ne pas toucher à ces coefficients, car ils sont calculés et mis à jour automatiquement}}
                <a class="pull-right btn btn-warning tooltips" id="bt_razLearning" style="position:relative;top:-7px;" title="Relance le processus d'apprentissage. N'oubliez pas de sauvegarde votre thermostat après la remise à 0."><i class="fa fa-times"></i> RaZ apprentissage</a>
            </div>
            <div class="form-group engine temporal">
                <label class="col-sm-2 control-label">{{Coefficient chauffage}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_heat" />
                </div>
                <label class="col-sm-2 control-label">{{Coefficient Clim}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_cool" />
                </div>
            </div>
            <div class="form-group engine temporal">
                <label class="col-sm-2 control-label">{{Apprentissage chaud}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_heat_autolearn" />
                </div>
                <label class="col-sm-2 control-label">{{Apprentissage froid}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_cool_autolearn" />
                </div>
            </div>
            <div class="form-group engine temporal">
                <label class="col-sm-2 control-label">{{Isolation chauffage}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_heat" />
                </div>
                <label class="col-sm-2 control-label">{{Isolation clim}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_cool" />
                </div>
            </div>
            <div class="form-group engine temporal">
                <label class="col-sm-2 control-label">{{Apprentissage isolation chaud}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_heat_autolearn" />
                </div>
                <label class="col-sm-2 control-label">{{Apprentissage isolation froid}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_cool_autolearn" />
                </div>
            </div>
            <div class="form-group engine hysteresis" style="display: none;">
                <label class="col-sm-2 control-label">{{Hystéresis (°C)}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="hysteresis_threshold" placeholder="1"/>
                </div>
                <label class="col-sm-2 control-label">{{Cron de contrôle}}</label>
                <div class="col-sm-2">
                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="hysteresis_cron" title="{{Cron de vérification des valeurs des sondes de témpérature, si votre thermostat ne démarre ou ne s'arrête pas correctement mettez en place cette vérification}}"/>
                </div>
                <div class="col-sm-1">
                    <i class="fa fa-question-circle cursor bt_pageHelp floatright" data-name="cronSyntaxe"></i>
                </div>
            </div>
        </fieldset>
    </form>
</div>

<div class="tab-pane" id="configureSchedule">
    <form class="form-horizontal">
        <fieldset>
            <br/>
            <div id="div_schedule"></div>
        </fieldset>
    </form>
</div>

</div>
</div>
</div>

<?php include_file('desktop', 'thermostat', 'js', 'thermostat');?>
<?php include_file('core', 'plugin.template', 'js');?>
