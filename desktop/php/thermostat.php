<?php
if (!isConnect('admin')) {
    throw new Exception('{{Error 401 Unauthorized}}');
}
sendVarToJS('eqType', 'thermostat');
$eqLogics = eqLogic::byType('thermostat');
?>
<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un thermostat}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes thermostats}}
        </legend>
        <?php
        if (count($eqLogics) == 0) {
            echo "<br/><br/><br/><center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>{{Vous n'avez pas encore de thermostat, cliquez sur Ajouter un thermostats pour commencer}}</span></center>";
        } else {
            ?>
            <div class="eqLogicThumbnailContainer">
                <?php
                foreach ($eqLogics as $eqLogic) {
                    echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
                    echo "<center>";
                    echo '<img src="plugins/thermostat/doc/images/thermostat_icon.png" height="105" width="95" />';
                    echo "</center>";
                    echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
                    echo '</div>';
                }
                ?>
            </div>
        <?php } ?>
    </div>

    <div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <div class="row">
            <div class="col-sm-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend><i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}  <i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i></legend>
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
                            <div class="col-sm-1">
                                <input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>
                            </div>
                            <label class="col-sm-4 control-label">{{Visible}}</label>
                            <div class="col-sm-1">
                                <input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">{{Moteur}}</label>
                            <div class="col-sm-6">
                                <select class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="engine" placeholder="" >
                                    <option value="temporal">Temporel</option>
                                    <option value="hysteresis">Hysteresis</option>
                                </select>
                            </div>
                        </div>
                    </fieldset> 
                </form>
            </div>
            <div class="col-sm-6">
                <form class="form-horizontal">
                    <fieldset>
                        <legend>{{Configuration}}</legend>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{Autoriser}}</label>
                            <div class="col-sm-6">
                                <select class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="allow_mode" title="{{Veuillez préciser les actions que le thermostat à le droit de faire en terme de chauffage et refroidissement.}}">
                                    <option value="all">Tout</option>
                                    <option value="heat">Chauffage uniquement</option>
                                    <option value="cool">Climatisation uniquement</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group expertModeVisible">
                            <label class="col-sm-3 control-label">{{Température min (°C)}}</label>
                            <div class="col-sm-2">
                                <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="order_min" title="{{Précisez l'écart de température que le thermostat est autorisé à piloter}}"/>
                            </div>
                            <label class="col-sm-2 control-label">{{max (°C)}}</label>
                            <div class="col-sm-2">
                                <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="order_max" title="{{Précisez l'écart de température que le thermostat est autorisé à piloter}}"/>
                            </div>
                        </div>
                        <div class='form-group  expertModeVisible'>
                            <label class="col-sm-3 control-label">{{Cron de répétition de commande}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="repeat_commande_cron" title="{{Cron de renvoi des commandes du thermostat (arrêt,chauffe, refroidis), si votre thermostat ne démarre ou ne s'arrête pas correctement mettez en place cette vérification}}"/>
                            </div>
                            <div class="col-sm-1">
                                <i class="fa fa-question-circle cursor bt_pageHelp floatright" data-name="cronSyntaxe"></i>
                            </div>
                        </div>
                        <div class='form-group  expertModeVisible'>
                            <label class="col-sm-3 control-label">{{Délai max entre 2 relevés de température (min)}}</label>
                            <div class="col-sm-6">
                                <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="maxTimeUpdateTemp" title="{{Délai maximum entre 2 relévés de température avant de mettre le thermostat en défaillance}}"/>
                            </div>
                        </div>
                    </fieldset> 
                </form>
            </div>
        </div>
        <hr/>
        <form class="form-horizontal">
            <fieldset>
                <div class="alert alert-info">
                    {{Veuillez ajouter vos sondes de température}}
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">{{Température intérieure}}</label>
                    <div class="col-sm-9">
                        <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_indoor" data-concat="1"/>
                    </div>
                    <div class="col-sm-1">
                        <a class="btn btn-default btn-sm listCmdInfo"><i class="fa fa-list-alt"></i></a>
                    </div>
                </div>
                <div class="form-group expertModeVisible">
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
                        <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="temperature_outdoor" data-concat="1"/>
                    </div>
                    <div class="col-sm-1">
                        <a class="btn btn-default btn-sm listCmdInfo"><i class="fa fa-list-alt"></i></a>
                    </div>
                </div>
            </fieldset> 
        </form>
        <hr/>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#configureAction" data-toggle="tab">{{Configuration des actions}}</a></li>
            <li><a href="#configureMode" data-toggle="tab">{{Configuration des modes}}</a></li>
            <li><a href="#configureWindows" data-toggle="tab">{{Configuration des ouvertures}}</a></li>
            <li><a href="#configureFailure" data-toggle="tab">{{Défaillance sonde de température}}</a></li>
            <li><a href="#configureAdvanced" data-toggle="tab">{{Configuration avancée}}</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="configureAction">
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
                            <a class="btn btn-success addWindow pull-right" data-type="window" style="position: relative;top: -7px;"><i class="fa fa-plus-circle"></i> Ajouter ouverture</a>
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
                        <a class="btn btn-success addFailure pull-right" data-type="failure" style="position: relative;top: -7px;"><i class="fa fa-plus-circle"></i> Ajouter action de defaillance</a>
                        <br/><br/>
                        <div id="div_failure"></div>
                    </fieldset> 
                </form> 
            </div>

            <div class="tab-pane" id="configureAdvanced">
                <form class="form-horizontal">
                    <fieldset>
                        <br/><br/>
                        <div class="form-group engine temporal">
                            <div class="expertModeVisible">
                                <div class="alert alert-warning">
                                    {{Pour une meilleur régulation, il est conseillé de ne pas toucher à ces coefficients, car ils sont calculés et mis à jour automatiquement}}
                                </div>
                                <label class="col-sm-2 control-label">{{Coefficient chauffage}}</label>
                                <div class="col-sm-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_heat" />
                                </div>
                                <label class="col-sm-2 control-label">{{Coefficient Clim}}</label>
                                <div class="col-sm-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_indoor_cool" />
                                </div>

                            </div>
                        </div>
                        <div class="form-group engine temporal">
                            <div class="expertModeVisible">
                                <label class="col-sm-2 control-label">{{Isolation chauffage}}</label>
                                <div class="col-sm-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_heat" />
                                </div>
                                <label class="col-sm-2 control-label">{{Isolation clim}}</label>
                                <div class="col-sm-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="coeff_outdoor_cool" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group engine temporal">
                            <div class="expertModeVisible">
                                <label class="col-sm-2 control-label">{{Offset chauffage (%)}}</label>
                                <div class="col-sm-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offset_heat" />
                                </div>
                                <label class="col-sm-2 control-label">{{Offset Clim (%)}}</label>
                                <div class="col-sm-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="offset_cool" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group engine temporal">
                            <div class="expertModeVisible">
                                <label class="col-sm-2 control-label">{{Auto-apprentissage}}</label>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="eqLogicAttr" data-l1key="configuration" data-l2key="autolearn" checked />
                                </div>
                                <label class="col-sm-2 control-label">{{Smart start}}</label>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="eqLogicAttr tooltips" data-l1key="configuration" data-l2key="smart_start" checked title="{{Autorise le thermostats à partir avant pour que la temperature soit égale à la consigne à l'heure voulu. Attention ne marche que si le thermostat est géré par le plugin agenda}}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group engine temporal">
                            <div class='expertModeVisible'>
                                <label class="col-sm-2 control-label">{{Cycle (min)}}</label>
                                <div class="col-sm-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="cycle" title="{{Durée des cycles de chauffe/climatisation (ne peut être inferieur à 15 min)}}"/>
                                </div>
                                <label class="col-sm-2 control-label">{{Temps de chauffe minimum (% du cycle)}}</label>
                                <div class="col-sm-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="minCycleDuration" title="{{% minimum de cycle a faire (sinon la mise en marche du chauffage est reporté au cyle suivant)}}" value="5"/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group engine hysteresis" style="display: none;">
                            <label class="col-sm-2 control-label">{{Hystéresis (°C)}}</label>
                            <div class="col-sm-2">
                                <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="hysteresis_threshold" placeholder="1"/>
                            </div>
                            <div class='expertModeVisible'>
                                <label class="col-sm-2 control-label">{{Cron de controle}}</label>
                                <div class="col-sm-2">
                                    <input type="text" class="eqLogicAttr form-control tooltips" data-l1key="configuration" data-l2key="hysteresis_cron" title="{{Cron de vérification des valeurs des sondes de témpérature, si votre thermostat ne démarre ou ne s'arrête pas correctement mettez en place cette vérification}}"/>
                                </div>
                                <div class="col-sm-1">
                                    <i class="fa fa-question-circle cursor bt_pageHelp floatright" data-name="cronSyntaxe"></i>
                                </div>
                            </div>
                        </div>
                    </fieldset> 
                </form> 
            </div>
        </div>

        <hr/>
        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<?php include_file('desktop', 'thermostat', 'js', 'thermostat'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>
