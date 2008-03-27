<?php
/**
 * +---------------------------------------------------------------------------+
 * | LL                             LLLL   LL     L   LLLL                 LL  |
 * | LL                            LL   L  LLL   LL  LL   L               LLL  |
 * | LL      LLLL   LLLLL  LL  LL  LL      LLLL LLL  LL          LL  LL    LL  |
 * | LL         LL     LL  LL  LL  LL      L LLL LL  LLLLL       LL  LL    LL  |
 * | LL      LLLLL    LL    LLLL   LL      L  L  LL   LLLLL      LL  LL    LL  |
 * | LL     LL  LL   LL     LLLL   LL      L     LL      LL       LLLL     LL  |
 * | LL     LL  LL  LL       LL    LL   L  L     LL  L   LL       LLLL     LL  |
 * | LLLLLL  LLLLL  LLLLL    LL     LLLL   L     LL   LLLL         LL     LLLL |
 * |                        LL                                                 |
 * |                        LL                                                 |
 * +---------------------------------------------------------------------------+
 * | Copyright (c) 2007-2008 LazyCMS.net All rights reserved.                  |
 * +---------------------------------------------------------------------------+
 * | 许可协议，请查看源代码中附带的 LICENSE.txt 文件，                         |
 * | 或者访问 http://www.lazycms.net/ 获得详细信息。                           |
 * +---------------------------------------------------------------------------+
 * | Author: Lukin <mylukin@gmail.com>                                         |
 * +---------------------------------------------------------------------------+
 */
defined('CORE_PATH') or die('Restricted access!');
/**
 * PHP ZIP压缩类，不需任何组件支持，本文件来自开国外开源代码
 */
// zip *** *** www.LazyCMS.net *** ***
class zip{
    public $l1sec,$ctrl_dir = array();
    public $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";
    public $old_offset = 0;
    public $dirs = Array(".");
    // getList *** *** www.LazyCMS.net *** ***
    function getList($l1){
        // $l1:zip name
        $l2 = @fopen($l1,'rb'); if(!$l2){ return(0); }
        $I2 = $this->ReadCentralDir($l2,$l1);
        @rewind($l2);
        @fseek($l2,$I2['offset']);
        for ($i=0; $i<$I2['entries']; $i++) {
            $I3 = $this->ReadCentralFileHeaders($l2);
            $I3['index']     = $i;
            $I4['crc']       = strtoupper(dechex( $I3['crc'] ));
            $I4['size']      = $I3['size'];
            $I4['mtime']     = $I3['mtime'];
            $I4['index']     = $I3['index'];
            $I4['folder']    = ($I3['external']==0x41FF0010 || $I3['external']==16)?1:0;
            $I4['status']    = $I3['status'];
            $I4['comment']   = $I3['comment'];
            $I4['filename']  = $I3['filename'];
            $I4['sfilename'] = $I3['sfilename'];
            $I4['compressed_size'] = $I3['compressed_size'];
            $I1[] = $I4;
            unset($I3);
        }
        return $I1;
    }
    // add *** *** www.LazyCMS.net *** ***
    function add($l1,$l2) {
        // $l1:files $l2:compact
        if(!is_array($l1[0])) { $l1 = Array($l1); }
        for($i=0;$l1[$i];$i++){
            $I2 = $l1[$i];
            if(!in_Array(dirname($I2[0]),$this->dirs)){
                $this->addDir(dirname($I2[0]));
            }
            if(basename($I2[0])){
                $I1[basename($I2[0])]=$this->addFile($I2[1],$I2[0],$l2);
            }
        }
        return $I1;
    }
    // getFile *** *** www.LazyCMS.net *** ***
    function getFile() {
        $l1 = implode('',$this->datasec);
        $l2 = implode('',$this->ctrl_dir);
        return $l1.$l2.$this->eof_ctrl_dir.
            pack('v',sizeof($this->ctrl_dir)).pack('v',sizeof($this->ctrl_dir)).
            pack('V',strlen($l2)).pack('V',strlen($l1))."\x00\x00";
    }
    // addDir *** *** www.LazyCMS.net *** ***
    function addDir($l1) {
        // $l1:name
        $l1 = str_replace("\\","/",$l1);
        $fr = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00";

        $fr.= pack("V",0).pack("V",0).pack("V",0).pack("v",strlen($l1));
        $fr.= pack("v",0 ).$l1.pack("V",0).pack("V",0).pack("V",0);
        $this->datasec[] = $fr;

        $l2 = strlen(implode("",$this->datasec));

        $l3 = "\x50\x4b\x01\x02\x00\x00\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00";
        $l3.= pack("V",0).pack("V",0).pack("V",0).pack("v",strlen($l1));
        $l3.= pack("v",0 ).pack("v",0 ).pack("v",0 ).pack("v",0);
        $l3.= pack("V", 16 ).pack("V", $this -> old_offset ).$l1;

        $this->ctrl_dir[] = $l3;
        $this->old_offset = $l2;
        $this->dirs[] = $l1;
    }
    // addFile *** *** www.LazyCMS.net *** ***
    function addFile($l1,$l2,$l3=1){
        // $l1:data,$l2:name,$l3:compact
        $l2 = str_replace('\\', '/', $l2);
        $I2 = dechex($this->DosTime());
        $I3 = '\x'.$I2[6].$I2[7].'\x'.$I2[4].$I2[5].'\x'.$I2[2].$I2[3].'\x'.$I2[0].$I2[1];eval('$I3 = "'.$I3.'";');
        if ($l3) {
            $fr = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00".$I3;
        } else {
            $fr = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00".$I3;
        }
        $l4 = strlen($l1); $crc = crc32($l1);
        if ($l3) {
            $I4 = gzcompress($l1); $l5 = strlen($I4);
            $I4 = substr(substr($I4,0,strlen($I4)-4),2);
        }else{
            $I4 = $l1;
        }
        $l5 = strlen($I4);
        $fr.= pack('V',$crc).pack('V',$l5).pack('V',$l4);
        $fr.= pack('v',strlen($l2)).pack('v',0).$l2.$I4;
        $fr.= pack('V',$crc).pack('V',$l5).pack('V',$l4);

        $this->datasec[] = $fr;
        $l6 = strlen(implode('',$this->datasec));
        if ($l3) {
            $l7 = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00";
        } else {
            $l7 = "\x50\x4b\x01\x02\x14\x00\x0a\x00\x00\x00\x00\x00";
        }
        $l7.= $I3.pack('V',$crc).pack('V',$l5).pack('V',$l4);
        $l7.= pack('v',strlen($l2)).pack('v',0).pack('v',0);
        $l7.= pack('v',0 ).pack('v',0).pack('V',32);
        $l7.= pack('V',$this->old_offset );
        $l7.= $l2;

        $this->old_offset = $l6;
        $this->ctrl_dir[] = $l7;
        return true;
    }
    // DosTime *** *** www.LazyCMS.net *** ***
    function DosTime() {
        $I2 = getdate();
        if ($I2['year'] < 1980) {
            $I2['year'] = 1980; $I2['mon'] = 1;
            $I2['mday'] = 1;    $I2['hours'] = 0;
            $I2['minutes'] = 0; $I2['seconds'] = 0;
        }
        return (($I2['year'] - 1980) << 25) | ($I2['mon'] << 21) | ($I2['mday'] << 16) | ($I2['hours'] << 11) | ($I2['minutes'] << 5) | ($I2['seconds'] >> 1);
    }
    // Extract *** *** www.LazyCMS.net *** ***
    function Extract($l1,$l2,$l3=Array(-1)) {
        // $l1:zn,$l2:to,$l3:index
        if(!is_dir($l2)) { @mkdir($l2,0777); }
        $l4  = 0;
        $I2 = @fopen($l1,'rb');if(!$I2){ return(-1); }
        $I3 = $this->ReadCentralDir($I2,$l1);
        $l5 = $I3['offset'];
        if(!is_array($l3)){ $l3 = array($l3);  }
        for($i=0; $l3[$i];$i++){
            if(intval($l3[$i])!=$l3[$i] || $l3[$i]>$I3['entries']) {
                return(-1);
            }
        }
        for ($i=0; $i<$I3['entries']; $i++) {
            @fseek($I2,$l5);
            $I4 = $this->ReadCentralFileHeaders($I2);
            $I4['index'] = $i; $l5 = ftell($I2);
            @rewind($I2); fseek($I2,$I4['offset']);
            if(in_array("-1",$l3)||in_array($i,$l3)){
                $I1[$I4['filename']] = $this->ExtractFile($I4, $l2, $I2);
            }
        }
        fclose($I2);
        return $I1;
    }
    // ReadFileHeader *** *** www.LazyCMS.net *** ***
    function ReadFileHeader($l1) {
        $I2 = fread($l1, 30);
        $I3 = unpack('vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len',$I2);
        $I1['filename'] = fread($l1, $I3['filename_len']);
        if ($I3['extra_len'] != 0) {
            $I1['extra'] = fread($l1, $I3['extra_len']);
        } else { 
            $I1['extra'] = null; 
        }

        $I1['compression'] = $I3['compression']; $I1['size'] = $I3['size'];
        $I1['compressed_size'] = $I3['compressed_size'];
        $I1['crc'] = $I3['crc']; $I1['flag'] = $I3['flag'];
        $I1['mdate'] = $I3['mdate']; $I1['mtime'] = $I3['mtime'];

        if ($I1['mdate'] && $I1['mtime']){
            $hour    = ($I1['mtime']&0xF800) >> 11;
            $minute  = ($I1['mtime']&0x07E0) >> 5;
            $seconde = ($I1['mtime']&0x001F) * 2;
            $year    = (($I1['mdate']&0xFE00) >> 9) + 1980;
            $month   = ($I1['mdate']&0x01E0) >> 5;
            $day     = $I1['mdate']&0x001F;
            $I1['mtime'] = mktime($hour,$minute,$seconde,$month,$day,$year);
        }else{
            $I1['mtime'] = time();
        }
        $I1['sfilename'] = $I1['filename'];
        $I1['status'] = "ok";
        return $I1;
    }
    // ReadCentralFileHeaders *** *** www.LazyCMS.net *** ***
    function ReadCentralFileHeaders($l1){
        $I2 = fread($l1, 46);
        $I1 = unpack('vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $I2);

        if ($I1['filename_len'] != 0) {
            $I1['filename'] = fread($l1,$I1['filename_len']);
        } else {
            $I1['filename'] = null;
        }

        if ($I1['extra_len'] != 0) {
            $I1['extra'] = fread($l1, $I1['extra_len']);
        } else {
            $I1['extra'] = null;
        }

        if ($I1['comment_len'] != 0) {
            $I1['comment'] = fread($l1, $I1['comment_len']);
        } else {
            $I1['comment'] = null;
        }

        if ($I1['mdate'] && $I1['mtime']) {
            $hour    = ($I1['mtime'] & 0xF800) >> 11;
            $minute  = ($I1['mtime'] & 0x07E0) >> 5;
            $seconde = ($I1['mtime'] & 0x001F)*2;
            $year    = (($I1['mdate'] & 0xFE00) >> 9) + 1980;
            $month   = ($I1['mdate'] & 0x01E0) >> 5;
            $day     = $I1['mdate'] & 0x001F;
            $I1['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
        } else {
            $I1['mtime'] = time();
        }
        $I1['sfilename'] = $I1['filename'];
        $I1['status'] = 'ok';
        if (substr($I1['filename'], -1) == '/'){
            $I1['external'] = 0x41FF0010;
        }
        return $I1;
    }
    // ReadCentralDir *** *** www.LazyCMS.net *** ***
    function ReadCentralDir($l1,$l2) {
        // $l1:zip,$l2:zip name
        $l3 = filesize($l2);
        if ($l3 < 277) { $l4 = $l3; } else { $l4 = 277; }

        @fseek($l1,$l3-$l4);
        $pos   = ftell($l1);
        $bytes = 0x00000000;

        while ($pos < $l3) {
            $byte  = @fread($l1, 1);
            $bytes = ($bytes << 8) | Ord($byte);
            if ($bytes == 0x504b0506){ 
                $pos++;break;
            }
            $pos++;
        }

        $I2 = unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size',fread($l1,18));

        if ($I2['comment_size'] != 0) {
            $I1['comment'] = fread($l1, $I2['comment_size']);
        } else {
            $I1['comment'] = null;
        }
        $I1['entries']      = $I2['entries'];
        $I1['disk_entries'] = $I2['disk_entries'];
        $I1['offset']       = $I2['offset'];
        $I1['disk_start']   = $I2['disk_start'];
        $I1['size']         = $I2['size'];
        $I1['disk']         = $I2['disk'];
        return $I1;
    }
    // ExtractFile *** *** www.LazyCMS.net *** ***
    function ExtractFile($l1,$l2,$l3) {
        // $l1:header,$l2:to,$l3:zip
        $l1 = $this->ReadFileHeader($l3);

        if(substr($l2,-1)!="/") { $l2.="/"; }
        if(substr($l1['filename'],-1)=="/") {
            @mkdir($l2.$l1['filename']);
            return +2;
        }

        $pth = explode("/",dirname($l1['filename']));
        for($i=0,$tmp="";isset($pth[$i]);$i++){
            if(!$pth[$i]) { continue; }
            if(!is_dir($l2.$tmp.$pth[$i])) { @mkdir($l2.$pth[$i],0777); }
            $tmp.= $pth[$i]."/";
        }
        if(!($l1['external']==0x41FF0010)&&!($l1['external']==16)) {
            if ($l1['compression']==0){
                $fp = @fopen($l2.$l1['filename'], 'wb');
                if(!$fp) { return(-1); }
                $l4 = $l1['compressed_size'];

                while ($l4 != 0){
                    $l5 = ($l4 < 2048 ? $l4 : 2048);
                    $l6 = fread($l3, $l5);
                    $I2 = pack('a'.$l5, $l6);
                    @fwrite($fp, $I2, $l5);
                    $l4 -= $l5;
                }
                fclose($fp);
                touch($l2.$l1['filename'], $l1['mtime']);
            }else{
                $fp = @fopen($l2.$l1['filename'].'.gz','wb');
                if(!$fp) { return(-1); }
                $I2 = pack('va1a1Va1a1', 0x8b1f, Chr($l1['compression']),
                Chr(0x00), time(), Chr(0x00), Chr(3));

                fwrite($fp, $I2, 10);
                $l4 = $l1['compressed_size'];

                while ($l4 != 0) {
                    $l5 = ($l4 < 1024 ? $l4 : 1024);
                    $l6 = fread($l3, $l5);
                    $I2 = pack('a'.$l5, $l6);
                    @fwrite($fp, $I2, $l5);
                    $l4 -= $l5;
                }

                $I2 = pack('VV', $l1['crc'], $l1['size']);
                fwrite($fp, $I2,8); fclose($fp);

                $gzp = @gzopen($l2.$l1['filename'].'.gz','rb');
                if(!$gzp){
                    @gzclose($gzp); @unlink($l2.$l1['filename']);
                    die("Archive is compressed whereas ZLIB is not enabled.");
                }
                $fp = @fopen($l2.$l1['filename'],'wb');
                if(!$fp) { return(-1); }
                $l4 = $l1['size'];

                while ($l4 != 0) {
                    $l5 = ($l4 < 2048 ? $l4 : 2048);
                    $l6 = gzread($gzp, $l5);
                    $I2 = pack('a'.$l5, $l6);
                    @fwrite($fp, $I2, $l5);
                    $l4 -= $l5;
                }
                fclose($fp); gzclose($gzp);

                touch($l2.$l1['filename'], $l1['mtime']);
                @unlink($l2.$l1['filename'].'.gz');
            }
        }
        return true;
    }
}