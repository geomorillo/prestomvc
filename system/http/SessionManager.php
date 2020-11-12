<?php

/*
 * Todos los derechos reservados por Manuel Jhobanny Morillo Ordoñez 
 * 2015
 * Contacto: geomorillo@yahoo.com
 */

namespace system\http;

/**
 * Description of Session
 *
 * @author geomorillo
 */
use system\database\Database;
use SessionHandlerInterface;
use system\core\LogException;
class SessionManager implements SessionHandlerInterface
{

    protected $db;

    public function __construct()
    {
        try {
            $this->db = Database::connect();
            if (!$this->db) {//PROBAR ERROR DE CONEXION
                throw new LogException();
            } else {
                return TRUE;
            }
        } catch (LogException $ex) {
            $ex->logError();
        }
    }

    /**
     * 
     * @param type $save_path
     * @param type $name
     * @throws LogException
     */
    public function open($save_path, $name)
    {
        if ($this->db) {
            return TRUE;
        }
        return FALSE;
    }

    public function read($session_id)
    {
        $session = $this->db->table("session")->find($session_id);
        if (count($session)) {
            return base64_decode($session->{"session_data"});
        } else {
            return '';
        }
    }

    public function write($session_id, $session_data)
    {
        $session_data = base64_encode($session_data);
        //i won't use replace for compatibility
        date_default_timezone_set('America/Bogota');
        $now = date('Y-m-d H:i:s');
        $expires = date('Y-m-d H:i:s', strtotime($now . ' + 1 hour'));
        $resultId = $this->db->table("session")->find($session_id);
        if (!$resultId) {
            $session = array("id" => $session_id, "expires" => $expires, "session_data" => $session_data);
            if ($this->db->table("session")->insert($session)) {
                return TRUE;
            }
        } else {
            $session = array("id" => $session_id, "expires" => $expires, "session_data" => $session_data);
            if ($this->db->table("session")->update($session)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function close()
    {
        return TRUE;
    }

    public function destroy($session_id)
    {
        $result = $this->db->table('session')
                ->where('id', $session_id)
                ->delete();
        if ($result) {
            return TRUE;
        }
        return FALSE;
    }

    public function gc($maxlifetime)
    {
        $expired = "UNIX_TIMESTAMP(expires) + " . $maxlifetime . ")";
        $result = $this->db->table('session')
                ->where($expired, '<', $maxlifetime)
                ->delete();
        if ($result) {
            return TRUE;
        } 
        return FALSE;
    }

}
