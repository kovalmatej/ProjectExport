<?php

namespace Kanboard\Plugin\ProjectExport\Controller;
use Kanboard\Controller\BaseController;

/**
 * Export Controller
 *
 * @package  Kanboard\Plugin\ProjectExport\Controller
 * @author   Matej Kovaľ
 */
class ProjectExportController extends BaseController
{

    private function common($model, $method, $filename, $action, $page_title)
    {
        $project = $this->getProject();

        if ($this->request->isPost()) {
            $from = $this->request->getRawValue('from');
            $to = $this->request->getRawValue('to');
            
            $id = $this->request->getRawValue('TaskId');
            $title = $this->request->getRawValue('Title');
            $column = $this->request->getRawValue('Column');
            $status = $this->request->getRawValue('Status');
            $due_date = $this->request->getRawValue('DueDate');
            $creation_date = $this->request->getRawValue('CreationDate');
            $start_date = $this->request->getRawValue('StartDate');
            $time_estimated = $this->request->getRawValue('TimeEstimated');
            $time_spent = $this->request->getRawValue('TimeSpent');

            if ($from && $to) {
                $data = $this->$model->$method($project['id'], $from, $to, $id, $title, $column, $status, $due_date, $creation_date, $start_date, $time_estimated, $time_spent);

                $table = "";
                $styles = "
                  <style>
                    .export-table {
                      border-collapse: collapse;
                      text-align: center;
                      font-family: 'Arial';
                      width: 80%;
                      border-radius: 10px;
                      table-layout: fixed;
                    }

                    .export-table tr:firt-child {
                      border-radius: 0 0 8px 8px;
                    }

                    .export-table tr:last-child {
                      border-radius: 8px 8px 0 0;
                    }

                    .export-table thead tr {
                      background: #36304a;
                      font-size: 17px;
                      color: white;
                    }

                    .export-table tr {
                      height: 50px;
                      font-size: 15px;
                      color: grey;
                    }

                    .export-table td, .export-table th {
                      padding: 0;
                      width: 100%;
                    }

                    .export-table tr:nth-child(2n) {
                      background: #f5f5f5;
                    }
                  </style>";
                $table .= $styles;
                $i = 0;

                foreach($data as $row) {
                  if($i == 0) {
                    $table .= "<thead>";
                  }

                  $table .= "<tr>";
                  foreach($row as $cell) {
                    $i == 0 
                      ? $table .= "<th>" . $cell . "</th> "
                      : $table .= "<td>" . $cell . "</td> ";
                  }

                  $table .= "</tr>";

                  if($i == 0) {
                    $table .= "</thead>";
                  }
                  $i++;
                }

                $this->response->html(
                  "<table class='export-table'>" .
                  $table
                  . "</table>"
                );


                //$this->response->withFileDownload($filename.'.csv');
                //$this->response->csv($data);
            }
        } else {
            $this->response->html($this->template->render('export/'.$action, array(
                'values'  => array(
                    'project_id' => $project['id'],
                    'from'       => '',
                    'to'         => '',
                ),
                'errors'  => array(),
                'project' => $project,
                'title'   => $page_title,
            )));
        }
    }

    /**
     * Task export
     *
     * @access public
     */
    public function tasks()
    {
        $this->common('taskExport', 'export', t('Tasks'), 'tasks', t('Tasks Export'));
    }

    /**
     * Subtask export
     *
     * @access public
     */
    public function subtasks()
    {
        $this->common('subtaskExport', 'export', t('Subtasks'), 'subtasks', t('Subtasks Export'));
    }

    /**
     * Daily project summary export
     *
     * @access public
     */
    public function summary()
    {
        $project = $this->getProject();

        if ($this->request->isPost()) {
            $from = $this->request->getRawValue('from');
            $to = $this->request->getRawValue('to');

            if ($from && $to) {
                $from = $this->dateParser->getIsoDate($from);
                $to = $this->dateParser->getIsoDate($to);
                $data = $this->projectDailyColumnStatsModel->getAggregatedMetrics($project['id'], $from, $to);
                $this->response->withFileDownload(t('Summary').'.csv');
                $this->response->csv($data);
            }
        } else {
            $this->response->html($this->template->render('export/summary', array(
                'values'  => array(
                    'project_id' => $project['id'],
                    'from'       => '',
                    'to'         => '',
                ),
                'errors'  => array(),
                'project' => $project,
                'title'   => t('Daily project summary export'),
            )));
        }
    }

    /**
     * Transition export
     *
     * @access public
     */
    public function transitions()
    {
        $this->common('transitionExport', 'export', t('Transitions'), 'transitions', t('Task transitions export'));
    }
}