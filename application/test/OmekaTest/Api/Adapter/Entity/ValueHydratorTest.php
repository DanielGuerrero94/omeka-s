<?php
namespace OmekaTest\Api\Adapter\Entity;

use Omeka\Api\Adapter\ValueHydrator;
use Omeka\Entity\Property;
use Omeka\Entity\Resource;
use Omeka\Entity\Value;
use Omeka\Test\TestCase;

class ValueHydratorTest extends TestCase
{
    protected $adapter;
    protected $resource;

    public function setUp()
    {
        $this->adapter = $this->getMockForAbstractClass(
            'Omeka\Api\Adapter\AbstractEntityAdapter',
                array(), '', true, true, true,
                array('getEntityManager', 'getAdapter')
        );
        $this->resource = $this->getMock('Omeka\Entity\Resource');
        $this->resource->expects($this->any())
            ->method('getValues')
            ->will($this->returnValue(
                $this->getMock('Doctrine\Common\Collections\ArrayCollection')
            ));
        }

    public function testHydrateRemoves()
    {
        $nodeObject = array(
            'term' => array(
                array(
                    'value_id' => 'test-value_id',
                    'delete' => true,
                ),
            ),
        );

        $value = $this->getMock('Omeka\Entity\Value');
        $entityManager = $this->getEntityManager();
        $entityManager->expects($this->once())
            ->method('getReference')
            ->with(
                $this->equalTo('Omeka\Entity\Value'),
                $this->equalTo($nodeObject['term'][0]['value_id'])
            )
            ->will($this->returnValue($value));
        $entityManager->expects($this->once())
            ->method('remove')
            ->with($this->isInstanceOf('Omeka\Entity\Value'));
        $this->adapter->expects($this->exactly(2))
            ->method('getEntityManager')
            ->will($this->returnValue($entityManager));

        $hydrator = new ValueHydrator($this->adapter);
        $hydrator->hydrate($nodeObject, $this->resource);
    }

    public function testHydrateModifiesLiteral()
    {
        $nodeObject = array(
            'term' => array(
                array(
                    '@value' => 'test-@value',
                    '@language' => 'test-@language',
                    'value_id' => 'test-value_id',
                ),
            ),
        );

        $value = $this->getMock('Omeka\Entity\Value');
        $value->expects($this->once())
            ->method('setType')
            ->with($this->equalTo(Value::TYPE_LITERAL));
        $value->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo($nodeObject['term'][0]['@value']));
        $value->expects($this->once())
            ->method('setLang')
            ->with($this->equalTo($nodeObject['term'][0]['@language']));
        $value->expects($this->once())
            ->method('setValueResource')
            ->with($this->equalTo(null));
        $entityManager = $this->getEntityManager();
        $entityManager->expects($this->once())
            ->method('getReference')
            ->with(
                $this->equalTo('Omeka\Entity\Value'),
                $this->equalTo($nodeObject['term'][0]['value_id'])
            )
            ->will($this->returnValue($value));
        $this->adapter->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($entityManager));

        $hydrator = new ValueHydrator($this->adapter);
        $hydrator->hydrate($nodeObject, $this->resource);
    }

    public function testHydrateModifiesResource()
    {
        $nodeObject = array(
            'term' => array(
                array(
                    '@id' => 'test-@id',
                    'value_id' => 'test-value_id',
                    'value_resource_id' => 'test-value_resource_id',
                ),
            ),
        );

        $valueResource = $this->getMock('Omeka\Entity\Resource');
        $value = $this->getMock('Omeka\Entity\Value');
        $value->expects($this->once())
            ->method('setType')
            ->with($this->equalTo(Value::TYPE_RESOURCE));
        $value->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo(null));
        $value->expects($this->once())
            ->method('setLang')
            ->with($this->equalTo(null));
        $value->expects($this->once())
            ->method('setValueResource')
            ->with($this->identicalTo($valueResource));
        $entityManager = $this->getEntityManager();
        $entityManager->expects($this->once())
            ->method('getReference')
            ->with(
                $this->equalTo('Omeka\Entity\Value'),
                $this->equalTo($nodeObject['term'][0]['value_id'])
            )
            ->will($this->returnValue($value));
        $entityManager->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo('Omeka\Entity\Resource'),
                $this->equalTo($nodeObject['term'][0]['value_resource_id'])
            )
            ->will($this->returnValue($valueResource));
        $this->adapter->expects($this->exactly(2))
            ->method('getEntityManager')
            ->will($this->returnValue($entityManager));

        $hydrator = new ValueHydrator($this->adapter);
        $hydrator->hydrate($nodeObject, $this->resource);
    }

    public function testHydrateModifiesUri()
    {
        $nodeObject = array(
            'term' => array(
                array(
                    '@id' => 'test-@id',
                    'value_id' => 'test-value_id',
                ),
            ),
        );

        $value = $this->getMock('Omeka\Entity\Value');
        $value->expects($this->once())
            ->method('setType')
            ->with($this->equalTo(Value::TYPE_URI));
        $value->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo($nodeObject['term'][0]['@id']));
        $value->expects($this->once())
            ->method('setLang')
            ->with($this->equalTo(null));
        $value->expects($this->once())
            ->method('setValueResource')
            ->with($this->equalTo(null));
        $entityManager = $this->getEntityManager();
        $entityManager->expects($this->once())
            ->method('getReference')
            ->with(
                $this->equalTo('Omeka\Entity\Value'),
                $this->equalTo($nodeObject['term'][0]['value_id'])
            )
            ->will($this->returnValue($value));
        $this->adapter->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($entityManager));

        $hydrator = new ValueHydrator($this->adapter);
        $hydrator->hydrate($nodeObject, $this->resource);
    }

    public function testHydratePersistsLiteral()
    {
        $nodeObject = array(
            'term' => array(
                array(
                    '@value' => 'test-@value',
                    '@language' => 'test-@language',
                    'property_id' => 'test-property_id',
                ),
            ),
        );

        $property = $this->getMock('Omeka\Entity\Property');
        $entityManager = $this->getEntityManager();
        $entityManager->expects($this->once())
            ->method('getReference')
            ->with(
                $this->equalTo('Omeka\Entity\Property'),
                $this->equalTo($nodeObject['term'][0]['property_id'])
            )
            ->will($this->returnValue($property));
        $this->adapter->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($entityManager));

        $hydrator = new ValueHydrator($this->adapter);
        $hydrator->hydrate($nodeObject, $this->resource);
    }

    public function testHydratePersistsResource()
    {
        $nodeObject = array(
            'term' => array(
                array(
                    'property_id' => 'test-property_id',
                    'value_resource_id' => 'test-value_resource_id',
                ),
            ),
        );

        $valueResource = $this->getMock('Omeka\Entity\Resource');
        $property = $this->getMock('Omeka\Entity\Property');
        $entityManager = $this->getEntityManager();
        $entityManager->expects($this->once())
            ->method('getReference')
            ->with(
                $this->equalTo('Omeka\Entity\Property'),
                $this->equalTo($nodeObject['term'][0]['property_id'])
            )
            ->will($this->returnValue($property));
        $entityManager->expects($this->once())
            ->method('find')
            ->with(
                $this->equalTo('Omeka\Entity\Resource'),
                $this->equalTo($nodeObject['term'][0]['value_resource_id'])
            )
            ->will($this->returnValue($valueResource));
        $this->adapter->expects($this->exactly(2))
            ->method('getEntityManager')
            ->will($this->returnValue($entityManager));

        $hydrator = new ValueHydrator($this->adapter);
        $hydrator->hydrate($nodeObject, $this->resource);
    }

    public function testHydratePersistsUri()
    {
        $nodeObject = array(
            'term' => array(
                array(
                    '@id' => 'test-@id',
                    'property_id' => 'test-property_id',
                ),
            ),
        );

        $property = $this->getMock('Omeka\Entity\Property');
        $entityManager = $this->getEntityManager();
        $entityManager->expects($this->once())
            ->method('getReference')
            ->with(
                $this->equalTo('Omeka\Entity\Property'),
                $this->equalTo($nodeObject['term'][0]['property_id'])
            )
            ->will($this->returnValue($property));
        $this->adapter->expects($this->once())
            ->method('getEntityManager')
            ->will($this->returnValue($entityManager));

        $hydrator = new ValueHydrator($this->adapter);
        $hydrator->hydrate($nodeObject, $this->resource);
    }
}
