<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Settlement
{

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function calculateCurrentCreditDebitBalanceForAllUserGroup()
    {
        $users = $this->CI->Common_model->get_data_by_query("SELECT id FROM users");
        foreach ($users as $u) {
            $uid = $u['id'];
            // check credit_debit_balance_entry
            $cdb = $this->CI->Common_model->get_single_query("SELECT * FROM credit_debit_balance WHERE user_id = $uid");
            // get credit_debit records
            $cd = $this->CI->Common_model->get_single_query("SELECT SUM(credits) AS c, SUM(debits) AS d FROM credits_debits WHERE user_id = $uid AND balance_calculated = 'no'");
            $credit = $cd->c;
            $debit = $cd->d;
            $balance = $credit - $debit;
            if (isset($cdb) && !empty($cdb)) {
                $data = array(
                    'credit'      => $credit + $cdb->credit,
                    'debit'       => $debit + $cdb->debit,
                    'balance'     => $balance + $cdb->balance,
                    'updated_at'  => date('Y-m-d H:i:s')
                );
                $this->CI->Crud_model->edit_record('credit_debit_balance', $cdb->id, $data);
            } else {
                $credit = $cd->c;
                $debit = $cd->d;
                $balance = $credit - $debit;
                $data = array(
                    'user_id'     => $uid,
                    'credit'      => $credit,
                    'debit'       => $debit,
                    'balance'     => $balance,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s')
                );
                $this->CI->Crud_model->insert_record('credit_debit_balance', $data);
            }
            $q = $this->CI->db->where('user_id', $uid)
            ->where('balance_calculated', 'no')
            ->set('balance_calculated', 'yes')
            ->update('credits_debits');
        }
        return true;
    }

    public function calculateCreditDebitBalanceByUserId($uid)
    {
        // check credit_debit_balance_entry
        $cdb = $this->CI->Common_model->get_single_query("SELECT * FROM credit_debit_balance WHERE user_id = $uid");
        // get credit_debit records
        $cd = $this->CI->Common_model->get_single_query("SELECT SUM(credits) AS c, SUM(debits) AS d FROM credits_debits WHERE user_id = $uid AND balance_calculated = 'no'");
        $credit = $cd->c;
        $debit = $cd->d;
        $balance = $credit - $debit;
        $initialBalance = 0;
        $finalBalance = 0;
        if (isset($cdb) && !empty($cdb)) {
            $data = array(
                'credit'      => $credit + $cdb->credit,
                'debit'       => $debit + $cdb->debit,
                'balance'     => $balance + $cdb->balance,
                'updated_at'  => date('Y-m-d H:i:s')
            );
            $this->CI->Crud_model->edit_record('credit_debit_balance', $cdb->id, $data);
            // print_r($this->CI->db->last_query());die;
            $initialBalance = $cdb->balance;
            $finalBalance = $balance + $cdb->balance;
        } else {
            $credit = $cd->c;
            $debit = $cd->d;
            $balance = $credit - $debit;
            $data = array(
                'user_id'     => $uid,
                'credit'      => $credit,
                'debit'       => $debit,
                'balance'     => $balance,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            );
            $this->CI->Crud_model->insert_record('credit_debit_balance', $data);
            $finalBalance = $balance;
        }
        $q = $this->CI->db->where('user_id', $uid)
            ->where('balance_calculated', 'no')
            ->set('balance_calculated', 'yes')
            ->update('credits_debits');
        return array('final' => $finalBalance, 'initial' => $initialBalance);
    }

    public function calculateAllCreditDebitBalanceByUserId($uid)
    {
        // check credit_debit_balance_entry
        $cdb = $this->CI->Common_model->get_single_query("SELECT * FROM credit_debit_balance WHERE user_id = $uid");
        // get credit_debit records
        $cd = $this->CI->Common_model->get_single_query("SELECT SUM(credits) AS c, SUM(debits) AS d FROM credits_debits WHERE user_id = $uid");
        $credit = $cd->c;
        $debit = $cd->d;
        $balance = $credit - $debit;
        $initialBalance = 0;
        $finalBalance = 0;
        if (isset($cdb) && !empty($cdb)) {
            $data = array(
                'credit'      => $credit + $cdb->credit,
                'debit'       => $debit + $cdb->debit,
                'balance'     => $balance + $cdb->balance,
                'updated_at'  => date('Y-m-d H:i:s')
            );
            $this->CI->Crud_model->edit_record('credit_debit_balance', $cdb->id, $data);
            // print_r($this->CI->db->last_query());die;
            $initialBalance = $cdb->balance;
            $finalBalance = $balance + $cdb->balance;
        } else {
            $credit = $cd->c;
            $debit = $cd->d;
            $balance = $credit - $debit;
            $data = array(
                'user_id'     => $uid,
                'credit'      => $credit,
                'debit'       => $debit,
                'balance'     => $balance,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            );
            $this->CI->Crud_model->insert_record('credit_debit_balance', $data);
            $finalBalance = $balance;
        }
        $q = $this->CI->db->where('user_id', $uid)
            ->where('balance_calculated', 'no')
            ->set('balance_calculated', 'yes')
            ->update('credits_debits');
        return array('final' => $finalBalance, 'initial' => $initialBalance);
    }

    public function calculateCreditDebitBalanceForUserGroup()
    {
        // $users =$this->CI->Common_model->get_data_by_query("SELECT id FROM users WHERE id NOT IN (SELECT user_id FROM credit_debit_balance ) AND id IN (SELECT user_id FROM credits_debits)");
        $users = $this->CI->Common_model->get_data_by_query("SELECT id FROM users_with_groups WHERE group_id = 5");
        foreach ($users as $u) {
            $uid = $u['id'];
            $cdb = $this->CI->Common_model->get_single_query("SELECT * FROM credit_debit_balance WHERE user_id = $uid");
            $cd = $this->CI->Common_model->get_single_query("SELECT SUM(credits) AS c, SUM(debits) AS d FROM credits_debits WHERE user_id = $uid");
            $credit = $cd->c;
            $debit = $cd->d;
            $balance = $credit - $debit;
            if (isset($cdb)) {
                $data = array(
                    'credit'      => $credit,
                    'debit'       => $debit,
                    'balance'     => $balance,
                    'updated_at'  => date('Y-m-d H:i:s')
                );
                $this->CI->Crud_model->edit_record('credit_debit_balance', $cdb->id, $data);
            } else {
                $credit = $cd->c;
                $debit = $cd->d;
                $balance = $credit - $debit;
                $data = array(
                    'user_id'     => $u['id'],
                    'credit'      => $credit,
                    'debit'       => $debit,
                    'balance'     => $balance,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s')
                );
                $this->CI->Crud_model->insert_record('credit_debit_balance', $data);
            }
            $this->CI->db->where('user_id', $uid);
            $this->CI->db->set('balance_calculated', 'yes');
            $this->CI->db->update('credits_debits');
        }
    }

    public function calculateCreditDebitBalanceForNonUserGroup()
    {
        // $users =$this->CI->Common_model->get_data_by_query("SELECT id FROM users WHERE id NOT IN (SELECT user_id FROM credit_debit_balance ) AND id IN (SELECT user_id FROM credits_debits)");
        $users = $this->CI->Common_model->get_data_by_query("SELECT id FROM users_with_groups WHERE group_id != 5");
        foreach ($users as $u) {
            $uid = $u['id'];
            $cdb = $this->CI->Common_model->get_single_query("SELECT * FROM credit_debit_balance WHERE user_id = $uid");
            $cd = $this->CI->Common_model->get_single_query("SELECT SUM(credits) AS c, SUM(debits) AS d FROM credits_debits WHERE user_id = $uid");
            $credit = $cd->c;
            $debit = $cd->d;
            $balance = $credit - $debit;
            if (isset($cdb)) {
                $data = array(
                    'credit'      => $credit,
                    'debit'       => $debit,
                    'balance'     => $balance,
                    'updated_at'  => date('Y-m-d H:i:s')
                );
                $this->CI->Crud_model->edit_record('credit_debit_balance', $cdb->id, $data);
            } else {
                $credit = $cd->c;
                $debit = $cd->d;
                $balance = $credit - $debit;
                $data = array(
                    'user_id'     => $u['id'],
                    'credit'      => $credit,
                    'debit'       => $debit,
                    'balance'     => $balance,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s')
                );
                $this->CI->Crud_model->insert_record('credit_debit_balance', $data);
            }
            $this->CI->db->where('user_id', $uid);
            $this->CI->db->set('balance_calculated', 'yes');
            $this->CI->db->update('credits_debits');
        }
    }
}
