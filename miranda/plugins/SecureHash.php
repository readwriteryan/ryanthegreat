<?php
namespace miranda\plugins;

Class SecureHash
{
    public static function getSalts($intDataLength)
    {
	$length = (int) intval($intDataLength) % 40;
	
	$salt_box = [
		    [0x3B,0xF1,0xA8,0xE1,0xA0,0xF2,0x66,0xEC,0x2C,0x81],
		    [0xE2,0x80,0x61,0x77,0x20,0xC5,0xCB,0x77,0x45,0x8F],
		    [0x68,0x2D,0xE0,0x91,0xC2,0x2B,0x5B,0xCB,0xC3,0x2F],
		    [0x3F,0x50,0xAC,0x7F,0x20,0xE8,0x80,0x67,0x10,0x8E],
		    [0x2A,0x98,0x55,0x32,0x96,0x1D,0x56,0xD0,0x34,0xF5],
		    [0x5E,0xED,0xE4,0x40,0x4C,0xE7,0x77,0xFD,0xD3,0xF6],
		    [0xA1,0x3F,0x81,0x0B,0x5F,0x0B,0xEF,0x8C,0x3D,0xD8],
		    [0xAB,0x02,0x30,0xF0,0x3F,0x7D,0x1B,0xD9,0xCD,0x42],
		    [0xD5,0xDB,0x46,0x88,0x35,0x0E,0x01,0x03,0x10,0x44],
		    [0xFB,0x21,0x7F,0xA3,0xDF,0x5A,0x43,0x59,0x9E,0x1C],
		    [0x32,0xB4,0x3B,0x74,0xBC,0x9B,0x8C,0xCF,0xB6,0x29],
		    [0x58,0x3A,0xB9,0x34,0x85,0x1B,0x8C,0xFC,0x77,0x03],
		    [0x3A,0xEB,0x6A,0xA2,0x1C,0xB7,0x5F,0x20,0x96,0x41],
		    [0xB5,0xC4,0x1C,0x09,0x31,0xC6,0xBB,0x22,0x6C,0x47],
		    [0x90,0xFE,0x01,0xE9,0x73,0xA3,0x97,0x7B,0x38,0x46],
		    [0x91,0xD5,0xCF,0x72,0xCC,0xA5,0xD9,0x4D,0xB2,0x62],
		    [0x61,0x6F,0x8E,0xAB,0x6F,0x2F,0x0F,0x5D,0xED,0xFB],
		    [0xAA,0x07,0x34,0x5F,0x7C,0x1E,0xD7,0xD5,0x68,0x1C],
		    [0x73,0xC8,0x31,0x52,0xE5,0xD8,0xFD,0x03,0x26,0x2A],
		    [0x24,0x8F,0xC6,0x52,0xE7,0xF6,0xAB,0xED,0xC4,0xFA],
		    [0x9D,0x39,0xA4,0xDD,0xA0,0x7C,0x50,0xC2,0x62,0x45],
		    [0x93,0xB4,0xE9,0x2B,0xE2,0x67,0xFA,0x1A,0xEF,0x79],
		    [0x0F,0x55,0xF2,0x75,0x5C,0xAE,0x06,0x3F,0x05,0xAE],
		    [0x80,0x57,0xA7,0x19,0x69,0x26,0x88,0xC7,0xB3,0xD0],
		    [0x08,0xF0,0xE2,0xFE,0x8A,0xC1,0xB2,0x86,0x9A,0xF0],
		    [0x42,0x7C,0xA3,0x36,0x71,0x67,0x96,0x87,0x88,0x51],
		    [0x22,0x2D,0x4B,0x3E,0x63,0xEA,0x56,0x60,0x9A,0x71],
		    [0xBD,0x94,0x31,0xCC,0x92,0xEC,0x40,0x99,0xBD,0x71],
		    [0x16,0xC7,0xC2,0x76,0xCE,0x88,0xC1,0xC0,0x39,0xCC],
		    [0xBB,0x9F,0xB2,0x97,0x00,0x91,0x75,0x94,0x71,0xB4],
		    [0x03,0xDC,0x49,0xD7,0x3A,0x4E,0x5D,0xE0,0x07,0x81],
		    [0x81,0x3F,0x7C,0x4F,0xDF,0x3D,0x20,0xA7,0xA7,0xFE],
		    [0x13,0xC6,0x1B,0x72,0x3F,0x3B,0x06,0xB8,0x3F,0x71],
		    [0xEA,0xE1,0xB8,0x86,0x08,0x41,0xC0,0x8D,0xCF,0x0F],
		    [0xE3,0xAF,0xA6,0x7E,0x5E,0xF4,0x9D,0x55,0x6E,0xB7],
		    [0x7F,0x83,0x01,0x1C,0x01,0xCF,0x56,0xF0,0xC6,0xE2],
		    [0x5D,0x6E,0xBC,0x42,0xBF,0x92,0xF2,0x6F,0x47,0xC4],
		    [0x88,0x3E,0xF3,0xEC,0x9C,0xEF,0xA5,0xE6,0x22,0x9E],
		    [0x12,0x00,0x56,0x09,0xEC,0xE6,0x52,0x45,0x53,0x1F],
		    [0x00,0xEA,0x6C,0x18,0xEA,0x60,0xD7,0x12,0xE1,0xCC]
		    ];
	
	$presalt 	= pack('C*', $salt_box[$length][0], $salt_box[$length][1], $salt_box[$length][2], $salt_box[$length][3], $salt_box[$length][4], $salt_box[$length][5], $salt_box[$length][6], $salt_box[$length][7], $salt_box[$length][8], $salt_box[$length][9]);
	$postsalt 	= pack('C*', $salt_box[40 - $length][0], $salt_box[40 - $length][1], $salt_box[40 - $length][2], $salt_box[40 - $length][3], $salt_box[40 - $length][4], $salt_box[40 - $length][5], $salt_box[40 - $length][6], $salt_box[40 - $length][7], $salt_box[40 - $length][8], $salt_box[40 - $length][9]);
    
	return [$presalt, $postsalt];    
    }
    
    public static function hash($strHashType, $hashData, $strUnique = '', $boolRawOutput = false)
    {
	if(in_array($strHashType, hash_algos()))
	{
	    list($presalt, $postsalt) = self::getSalts(strlen($hashData));
	    
	    return hash($strHashType, $presalt . $hashData . $strUnique . $postsalt, $boolRawOutput ? true: false);
	}
	
	return false;
    }
}
?>