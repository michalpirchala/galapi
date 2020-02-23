<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Galleries Model
 *
 * @property \App\Model\Table\ImagesTable&\Cake\ORM\Association\HasMany $Images
 *
 * @method \App\Model\Entity\Gallery get($primaryKey, $options = [])
 * @method \App\Model\Entity\Gallery newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Gallery[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Gallery|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Gallery saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Gallery patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Gallery[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Gallery findOrCreate($search, callable $callback = null, $options = [])
 */
class GalleriesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('galleries');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->hasMany('Images', [
            'foreignKey' => 'gallery_id',
            'dependent' => true,
            'cascadeCallbacks' => true,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->add('name', 'custom', [
                'rule' => function ($value, $context) {
                    return strpos($value, '/') === false;
                },
                'message' => 'Name can not contain slash',
            ]);

        return $validator;
    }

    /**
     * [prepareImagesData description]
     * @param  \App\Model\Entity\Gallery $gallery gallery to extract image info from
     * @return array of images data
     */
    public function prepareImagesData(\App\Model\Entity\Gallery $gallery)
    {
        $data = [];
        foreach ($gallery->images as $image) {
            $data[] = [
                'path' => rawurlencode($image['filename']),
                'fullpath' => $gallery->get('path') . DS . rawurlencode($image['filename']),
                'name' => $image['name'],
                'modified' => $image->modified->i18nFormat("yyyy-MM-dd'T'HH:mm:ss.SZ"),
            ];
        }

        return $data;
    }
}
