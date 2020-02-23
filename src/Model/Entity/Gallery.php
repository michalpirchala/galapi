<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Gallery Entity
 *
 * @property int $id
 * @property string $name
 *
 * @property \App\Model\Entity\Image[] $images
 */
class Gallery extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'images' => true,
    ];

    protected $_virtual = ['path'];

    /**
     * Returns path created from name
     * @return string rawurlencoded name
     */
    protected function _getPath()
    {
        return rawurlencode($this->name);
    }
}
