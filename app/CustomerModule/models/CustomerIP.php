<?php



/**
 * IP address of customer
 *
 * @Table @Entity
 * @ae:links(module="Customer", presenter="dashboard", alias="ip", common={"add", "edit", "remove"})
 */
class CustomerIP extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Customer $customer
   * @ManyToOne(targetEntity="Customer", inversedBy="IPs")
   * @JoinColumn(name="custId", referencedColumnName="custId")
   * @ae:required @ae:immutable
   */
  protected $customer;

  /**
   * @var string $IP
   * @Column(name="IP", type="string", length=20, nullable=false)
   * @ae:name
   */
  protected $IP;

  /**
   * @var integer $netmask
   * @Column(name="netmask", type="integer", length=2, nullable=false)
   */
  protected $netmask;

  /**
   * @var string $IPold
   * @Column(name="IPold", type="string", length=20, nullable=true)
   */
  protected $IPold;

  /**
   * @var string $IPverej
   * @Column(name="IPverej", type="string", length=20, nullable=true)
   */
  protected $IPpublic;

  /**
   * @var string $MAC
   * @Column(name="MAC", type="string", length=20, nullable=true)
   */
  protected $MAC;

  /**
   * @var string $visibleMAC
   * @Column(name="visibleMAC", type="string", length=20, nullable=true)
   */
  protected $visibleMAC;

  /**
   * @var integer $adress
   * @ManyToOne(targetEntity="CustomerAddress")
   * @JoinColumn(name="address", referencedColumnName="ID")
   */
  protected $address;

  /**
   * @var AP $l2parent
   * @ManyToOne(targetEntity="AP")
   * @JoinColumn(name="l2parent", referencedColumnName="ID")
   */
  protected $l2parent;

  /**
   * @var string $l2parentIf
   * @Column(name="l2parentIf", type="string", length=50, nullable=true)
   */
  protected $l2parentIf;

  /**
   * @var integer $l3parent
   * @ManyToOne(targetEntity="AP")
   * @JoinColumn(name="l3parent", referencedColumnName="ID")
   */
  protected $l3parent;

  /**
   * @var string $l3parentIf
   * @Column(name="l3parentIf", type="string", length=50, nullable=true)
   */
  protected $l3parentIf;

  /**
   * @var string $poznamka
   * @Column(name="poznamka", type="string", length=255, nullable=true)
   */
  protected $poznamka;

  /**
   * @var enum $encType
   * @Column(name="encType", type="enum", nullable=false)
   * @ae:defaultValue("none") @ae:enumValues("none,wep,wpa")
   */
  protected $encType;

  /**
   * @var string $encKey
   * @Column(name="encKey", type="string", length=50, nullable=true)
   */
  protected $encKey;

  /**
   * @var enum $router
   * @Column(name="router", type="enum", nullable=false)
   * @ae:defaultValue("none") @ae:enumValues("none,cable,wifi,combo")
   */
  protected $router;

  /**
   * @var boolean $routerVlastni
   * @Column(name="routerVlastni", type="boolean", nullable=true)
   */
  protected $routerVlastni;

  /**
   * @var enum $voip
   * @Column(name="voip", type="enum", nullable=false)
   * @ae:defaultValue("none") @ae:enumValues("none,hlas,data")
   */
  protected $voip;

  /**
   * @var boolean $vlastniRychlost
   * @Column(name="vlastniRychlost", type="boolean", nullable=false)
   */
  protected $vlastniRychlost;

  /**
   * @var string $APIP
   * @Column(name="APIP", type="string", length=15, nullable=true)
   */
  protected $APIP;

  /**
   * @var string $APMAC
   * @Column(name="APMAC", type="string", length=20, nullable=true)
   */
  protected $APMAC;

}
