
/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */


 $(".in_datepicker").datepicker();

 $('#bt_validChangeDate').on('click', function () {
    jeedom.history.chart = [];
    $('#div_displayEquipement').packery('destroy');
    displayThermostat(object_id, $('#in_startDate').value(), $('#in_endDate').value());
});

 displayThermostat(object_id);


 function displayThermostat(object_id) {
    $.ajax({
        type: 'POST',
        url: 'plugins/thermostat/core/ajax/thermostat.ajax.php',
        data: {
            action: 'getThermostat',
            object_id: object_id,
            version: 'dashboard'
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            }
            var icon = '';
            if (isset(data.result.object.display) && isset(data.result.object.display.icon)) {
                icon = data.result.object.display.icon;
            }
            $('.objectName').empty().append(icon + ' ' + data.result.object.name);
            $('#div_displayEquipement').empty();
            $('#div_charts').empty();
            for (var i in data.result.eqLogics) {
                $('#div_displayEquipement').append(data.result.eqLogics[i].html);
                var div_graph = '<legend>' + data.result.eqLogics[i].eqLogic.name + '</legend>'
                div_graph += '<div class="chartContainer" id="div_graph' + data.result.eqLogics[i].eqLogic.id + '"></div>';
                $('#div_charts').append(div_graph);
                graphThermostat(data.result.eqLogics[i].eqLogic.id);
            }
            positionEqLogic();
            $('#div_displayEquipement').packery({columnWidth: 1});
        }
    });
}

function graphThermostat(_eqLogic_id) {
    jeedom.eqLogic.getCmd({
        id: _eqLogic_id,
        error: function (error) {
            $('#div_alert').showAlert({message: error.message, level: 'danger'});
        },
        success: function (cmds) {
            for (var i  in cmds) {
                if (cmds[i].logicalId == 'actif' || cmds[i].logicalId == 'order') {
                    var color = '';
                    if (cmds[i].logicalId == 'order') {
                        color = '#27ae60';
                        jeedom.history.drawChart({
                            cmd_id: cmds[i].id,
                            el: 'div_graph' + _eqLogic_id,
                            start: $('#in_startDate').value(),
                            end: $('#in_endDate').value(),
                            option: {
                                graphStep: 1,
                                graphColor: color
                            }
                        });
                    }
                    if (cmds[i].logicalId == 'actif') {
                        color = '#2c3e50';
                        jeedom.history.drawChart({
                            cmd_id: cmds[i].id,
                            el: 'div_graph' + _eqLogic_id,
                            start: $('#in_startDate').value(),
                            end: $('#in_endDate').value(),
                            option: {
                                graphStep: 1,
                                graphColor: color,
                                graphScale : 1,
                                graphType : 'area'
                            }
                        });
                    }
                }
                if (cmds[i].logicalId == 'temperature') {
                    jeedom.history.drawChart({
                        cmd_id: cmds[i].id,
                        el: 'div_graph' + _eqLogic_id,
                        start: $('#in_startDate').value(),
                        end: $('#in_endDate').value(),
                        option: {
                            graphColor: '#f39c12'
                        }
                    });
                }
            }

        }
    });
}