<?php

abstract class MY_Model extends CI_Model {

    public function IdNamePairList($list) {
        $outlist = array();
        foreach ($list as $key => $value) {
            $obj = new stdClass();
            $obj->id = $key;
            $obj->name = $value;
            $outlist[] = $obj;
        }
        return $outlist;
    }

    public function LimitClause($page, $size = 10) {
        return " limit " . ($page - 1 ) * $size . " , " . $size;
    }

    public abstract function TableName();

    public function BaseSync() {
        $queries = array(
        );
        foreach ($queries as $q) {
            $this->db->query($q);
        }
    }

    public function _addImageToList(&$list) {
        foreach ($list as $value) {
            if ($value->has_image == "yes") {
                $value->image = base_url("uploads/" . $this->TableName() . "/" . $value->image_name);
            } else {
                $value->image = base_url("uploads/defaults/" . $this->TableName() . ".png");
            }
        }
        return $list;
    }

    public function _addImageToSingle(&$value) {

        if ($value->has_image == "yes") {
            $value->image = base_url("uploads/" . $this->TableName() . "/" . $value->image_name);
        } else {
            $value->image = base_url("uploads/defaults/" . $this->TableName() . ".png");
        }

        return $value;
    }

    public function Upsert($record) {
        if ($record->id > 0) {
            return $this->Update($record);
        } else {
            return $this->Create($record);
        }
    }

    public function Create($record) {
        $this->db->insert($this->TableName(), (array) $record);
        return $this->db->insert_id();
    }

    public function RawWrite($sql) {
        $this->db->query($sql);
    }

    public function Update($record) {
        $this->db->update($this->TableName(), (array) $record, array("id" => $record->id));
        $this->BaseSync();
    }

    public function Delete($id) {
        $this->db->delete($this->TableName(), array("id" => $id));
    }

    public function Read() {
        return $this->db->query("select * from " . $this->TableName() . " order by id ")->result();
    }

    public function ReadSorted() {
        return $this->db->query("select * from " . $this->TableName() . " order by sort_order desc ")->result();
    }

    public function ReadSortedAssoc() {
        $list = $this->db->query("select * from " . $this->TableName() . " order by sort_order desc ")->result();
        $outlist = array();
        foreach ($list as $value) {
            $outlist[$value->id] = $value;
        }

        return $outlist;
    }

    public function ReadAllAssoc() {
        $list = $this->db->query("select * from " . $this->TableName() . " order by sort_order asc ")->result();
        $outlist = array();
        foreach ($list as $value) {
            $outlist[$value->id] = $value;
        }

        return $outlist;
    }

    public function ReadDesc() {
        return $this->db->query("select * from " . $this->TableName() . " order by id desc")->result();
    }

    public function ReadMap() {
        $list = $this->db->query("select * from " . $this->TableName() . " order by id ")->result();

        $outlist = array();
        foreach ($list as $value) {
            $outlist[$value->id] = $value;
        }

        return $outlist;
    }

    public function ReadMapRawIdName($sql, $id = "id", $name = "name") {
        $list = $this->db->query($sql)->result();

        $outlist = array();
        foreach ($list as $value) {
            $outlist[$value->$id] = $value->$name;
        }

        return $outlist;
    }

    public function ReadForAdmin($admin_id) {
        return $this->db->query("select * from " . $this->TableName() . " where admin_id = $admin_id ")->result();
    }

    public function ReadRaw($sql) {
        return $this->db->query($sql)->result();
    }

    public function ReadAssoc($sql, $id_col = "id", $name_col = "name") {
        $list = $this->db->query($sql)->result();
        $outlist = array();
        foreach ($list as $value) {
            $outlist[$value->$id_col] = $value->$name_col;
        }
        return $outlist;
    }

    private function RemovePrefix($s) {
        //return substr($s, 3);
        return $s;
    }

    public function Find($id) {
        $row = $this->db->query("select * from " . $this->TableName() . " where id = $id ")->row();
        if (isset($row)) {
            return $this->Convert($this->RemovePrefix($this->TableName()) . "_model", $row);
        }
        $class = new ReflectionClass(ucfirst($this->RemovePrefix($this->TableName()) . "_model"));
        return $class->newInstanceArgs();

        //return $row;
    }

    public function FindCustom($sql) {
        $row = $this->db->query($sql)->row();
        if (isset($row)) {
            return $this->Convert($this->RemovePrefix($this->TableName()) . "_model", $row);
        }
        $class = new ReflectionClass(ucfirst($this->RemovePrefix($this->TableName()) . "_model"));
        return $class->newInstanceArgs();

        //return $row;
    }

    public function ExistsCustom($sql) {
        $row = $this->db->query($sql)->row();
        if (isset($row)) {
            return $this->Convert($this->RemovePrefix($this->TableName()) . "_model", $row);
        }
        return null;

        //return $row;
    }

    public function Convert($table, $object) {
        $r = new ReflectionClass(ucfirst($table));
        $objInstance = $r->newInstanceArgs();
        $props = get_object_vars($object);
        foreach ($props as $key => $value) {
            $objInstance->$key = $value;
        }
        return $objInstance;
    }

    public function Count($sql) {
        $object = $this->ExistsCustom($sql);
        if ($object) {
            $result = $object->count;
        }
        return $result;
    }

    public function Pages($count, $pageSize, $page) {
        $count = (int) (($count - 1) / $pageSize + 1);
        $from = $page - 5;
        $to = $page + 5;
        if ($from < 1) {
            $from = 1;
        }
        if ($to > $count) {
            $to = $count;
        }
        return array("from" => $from, "to" => $to);
    }

}
