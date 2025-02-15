<?php

declare(strict_types=1);

/**
 * This file is part of ILIAS, a powerful learning management system
 * published by ILIAS open source e-Learning e.V.
 *
 * ILIAS is licensed with the GPL-3.0,
 * see https://www.gnu.org/licenses/gpl-3.0.en.html
 * You should have received a copy of said license along with the
 * source code, too.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 * https://www.ilias.de
 * https://github.com/ILIAS-eLearning
 *
 *********************************************************************/

/**
 * Export Container
 * @author    Stefan Meyer <meyer@leifos.com>
 */
class ilExportContainer extends ilExport
{
    private string $cont_export_dir = '';
    private ?ilXmlWriter $cont_manifest_writer = null;
    private ilExportOptions $eo;

    /**
     * Constructor
     * @param ilExportOptions $eo
     */
    public function __construct(ilExportOptions $eo)
    {
        $this->eo = $eo;
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function exportObject(string $a_type, int $a_id, string $a_target_release = ""): array
    {
        $log = $GLOBALS['DIC']->logger()->exp();

        // if no target release specified, use latest major release number
        if ($a_target_release == "") {
            $v = explode(".", ILIAS_VERSION_NUMERIC);
            $a_target_release = $v[0] . "." . $v[1] . ".0";
        }

        // Create base export directory
        ilExport::_createExportDirectory($a_id, "xml", $a_type);
        $export_dir = ilExport::_getExportDirectory($a_id, "xml", $a_type);
        $ts = time();
        $sub_dir = $ts . "__" . IL_INST_ID . "__" . $a_type . "_" . $a_id;

        $this->cont_export_dir = $export_dir . DIRECTORY_SEPARATOR . $sub_dir;
        ilFileUtils::makeDirParents($this->cont_export_dir);

        $log->debug('Using base directory: ' . $this->export_run_dir);

        $this->manifestWriterBegin($a_type, $a_id, $a_target_release);
        $this->addContainer();
        $this->addSubitems($a_id, $a_type, $a_target_release);
        $this->manifestWriterEnd($a_type, $a_id, $a_target_release);

        ilFileUtils::zip($this->cont_export_dir, $this->cont_export_dir . '.zip');
        ilFileUtils::delDir($this->cont_export_dir);
        return [];
    }

    protected function manifestWriterBegin(string $a_type, int $a_id, string $a_target_release): void
    {
        $this->cont_manifest_writer = new ilXmlWriter();
        $this->cont_manifest_writer->xmlHeader();
        $this->cont_manifest_writer->xmlStartTag(
            'Manifest',
            array(
                "MainEntity" => $a_type,
                "Title" => ilObject::_lookupTitle($a_id),
                "TargetRelease" => $a_target_release,
                "InstallationId" => IL_INST_ID,
                "InstallationUrl" => ILIAS_HTTP_PATH
            )
        );
    }

    protected function addContainer(): void
    {
    }

    protected function addSubitems(int $a_id, string $a_type, string $a_target_release): void
    {
        $set_number = 1;
        foreach ($this->eo->getSubitemsForExport() as $ref_id) {
            // get last export file
            $obj_id = ilObject::_lookupObjId($ref_id);

            $expi = ilExportFileInfo::lookupLastExport($obj_id, 'xml', $a_target_release);

            if (!$expi instanceof ilExportFileInfo) {
                $this->log->warning('Cannot find export file for refId ' . $ref_id . ', type ' . ilObject::_lookupType($a_id));
                continue;
            }

            $exp_dir = ilExport::_getExportDirectory($obj_id, 'xml', ilObject::_lookupType($obj_id));
            $exp_full = $exp_dir . DIRECTORY_SEPARATOR . $expi->getFilename();

            $this->log->debug('Zip path ' . $exp_full);

            // Unzip
            ilFileUtils::unzip($exp_full, true, false);

            // create set directory
            ilFileUtils::makeDirParents($this->cont_export_dir . DIRECTORY_SEPARATOR . 'set_' . $set_number);

            // cut .zip
            $new_path_rel = 'set_' . $set_number . DIRECTORY_SEPARATOR . $expi->getBasename();
            $new_path_abs = $this->cont_export_dir . DIRECTORY_SEPARATOR . $new_path_rel;

            $this->log->debug($new_path_rel . ' ' . $new_path_abs);

            // Move export
            rename(
                $exp_dir . DIRECTORY_SEPARATOR . $expi->getBasename(),
                $new_path_abs
            );

            $this->log->debug($exp_dir . DIRECTORY_SEPARATOR . $expi->getBasename() . ' -> ' . $new_path_abs);

            // Delete latest container xml of source
            if ($a_id == $obj_id) {
                $expi->delete();
                if (file_exists($exp_full)) {
                    $this->log->info('Deleting' . $exp_full);
                    unlink($exp_full);
                }
            }

            $this->cont_manifest_writer->xmlElement(
                'ExportSet',
                array(
                    'Path' => $new_path_rel,
                    'Type' => ilObject::_lookupType($obj_id)
                )
            );
            ++$set_number;
        }
    }

    protected function manifestWriterEnd(string $a_type, int $a_id, string $a_target_release): void
    {
        $this->cont_manifest_writer->xmlEndTag('Manifest');
        $this->log->debug($this->cont_export_dir . DIRECTORY_SEPARATOR . 'manifest.xml');
        $this->cont_manifest_writer->xmlDumpFile($this->cont_export_dir . DIRECTORY_SEPARATOR . 'manifest.xml', true);
    }
}
