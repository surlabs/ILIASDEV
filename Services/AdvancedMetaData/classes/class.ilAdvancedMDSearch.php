<?php

declare(strict_types=1);
/*
    +-----------------------------------------------------------------------------+
    | ILIAS open source                                                           |
    +-----------------------------------------------------------------------------+
    | Copyright (c) 1998-2006 ILIAS open source, University of Cologne            |
    |                                                                             |
    | This program is free software; you can redistribute it and/or               |
    | modify it under the terms of the GNU General Public License                 |
    | as published by the Free Software Foundation; either version 2              |
    | of the License, or (at your option) any later version.                      |
    |                                                                             |
    | This program is distributed in the hope that it will be useful,             |
    | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
    | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
    | GNU General Public License for more details.                                |
    |                                                                             |
    | You should have received a copy of the GNU General Public License           |
    | along with this program; if not, write to the Free Software                 |
    | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
    +-----------------------------------------------------------------------------+
*/

/**
 * @author  Stefan Meyer <meyer@leifos.com>
 * @ingroup ServicesAdvancedMetaData
 */
class ilAdvancedMDSearch extends ilAbstractSearch
{
    protected ?ilAdvancedMDFieldDefinition $definition = null;
    protected ?ilADTSearchBridge $adt = null;

    public function __construct($query_parser)
    {
        parent::__construct($query_parser);
    }

    public function setDefinition(ilAdvancedMDFieldDefinition $a_def): void
    {
        $this->definition = $a_def;
    }

    public function getDefinition(): ilAdvancedMDFieldDefinition
    {
        return $this->definition;
    }

    public function setSearchElement(ilADTSearchBridge $a_adt): void
    {
        $this->adt = $a_adt;
    }

    public function getSearchElement(): ilADTSearchBridge
    {
        return $this->adt;
    }

    public function performSearch(): ilSearchResult
    {
        $this->query_parser->parse();

        $locate = null;
        $parser_value = $this->getDefinition()->getSearchQueryParserValue($this->getSearchElement());
        if ($parser_value) {
            $this->setFields(
                [
                    $this->getSearchElement()->getSearchColumn()
                ]
            );
            $locate = $this->__createLocateString();
        }

        $search_type = strtolower(substr(get_class($this), 12, -6));

        $res_field = $this->getDefinition()->searchObjects(
            $this->getSearchElement(),
            $this->query_parser,
            $this->getFilter(),
            $locate,
            $search_type
        );

        if (is_array($res_field)) {
            foreach ($res_field as $row) {
                $found = is_array($row["found"]) ? $row["found"] : array();
                $this->search_result->addEntry($row["obj_id"], $row["type"], $found);
            }
            return $this->search_result;
        }
        return $this->search_result;
    }
}
