<?php
/**
 * This file is part of isou project.
 *
 * @author  Université Rennes 2
 * @license The Unlicense <http://unlicense.org>
 */

declare(strict_types=1);

use UniversiteRennes2\Isou\Event;

if (isset($PAGE_NAME[2]) === false) {
    $PAGE_NAME[2] = '';
}

if (isset($PAGE_NAME[3]) === false) {
    $PAGE_NAME[3] = '';
}

switch ($PAGE_NAME[2]) {
    case 'service':
        if (ctype_digit($PAGE_NAME[3]) === true) {
            $lastyear = new DateTime('-1 year');

            $sql = 'SELECT DISTINCT ed.description'.
                ' FROM events_descriptions ed'.
                ' JOIN events e ON ed.id = e.ideventdescription'.
                ' WHERE ed.autogen = 0'.
                ' AND e.startdate >= :startdate'.
                ' AND e.idservice = :idservice'.
                ' ORDER BY UPPER(ed.description)';
            $query = $DB->prepare($sql);

            $parameters = array();
            $parameters[':startdate'] = $lastyear->format('Y-m-d\TH:i:s');
            $parameters[':idservice'] = $PAGE_NAME[3];
            $query->execute($parameters);

            $descriptions = array();
            foreach ($query->fetchAll(PDO::FETCH_OBJ) as $record) {
                $label = substr($record->description, 0, 100);
                $value = $record->description;

                $descriptions[] = (object) array('label' => $label, 'value' => $value);
            }

            echo json_encode($descriptions);
            exit(0);
        }
        break;
}
