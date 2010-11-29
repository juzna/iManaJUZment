<?php



/**
 * AP
 *
 * @Table()
 * @Entity @ae:title("Access Points")
 */
class AP extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $name
     * @Column(name="name", type="string", length=100, nullable=false, unique=true)
     * @ae:title("AP's name") @ae:show
     */
    protected $name;

    /**
     * @var string $description 
     * @Column(name="description", type="string", length=255, nullable=true) @ae:title("Description") @ae:show
     */
    protected $description;

    /**
     * @var enum $mode
     * @Column(name="mode", type="enum", nullable=false) @ae:show
     */
    protected $mode;

    /**
     * @var string $IP
     * @Column(name="IP", type="string", length=15, nullable=false)
     * @ae:title("IP address") @ae:format("ip")
     */
    protected $IP;

    /**
     * @var integer $netmask
     * @Column(name="netmask", type="integer", length=2, nullable=false)
     * @ae:format("netmask")
     */
    protected $netmask;

    /**
     * @var integer $pvid
     * @Column(name="pvid", type="integer", length=4, nullable=false)
     * @ae:title("VLAN PVID") @ae:format("int")
     */
    protected $pvid;

    /**
     * @var boolean $snmpAllowed
     * @Column(name="snmpAllowed", type="boolean", nullable=false)
     * @ae:title("Is SNMP allowed?") @ae:format("bool")
     */
    protected $snmpAllowed;

    /**
     * @var string $snmpCommunity
     * @Column(name="snmpCommunity", type="string", length=50, nullable=true)
     */
    protected $snmpCommunity;

    /**
     * @var string $snmpPassword
     * @Column(name="snmpPassword", type="string", length=50, nullable=true)
     */
    protected $snmpPassword;

    /**
     * @var string $snmpVersion
     * @Column(name="snmpVersion", type="string", length=10, nullable=true)
     */
    protected $snmpVersion;

    /**
     * @var string $realm
     * @Column(name="realm", type="string", length=50, nullable=true)
     */
    protected $realm;

    /**
     * @var string $username
     * @Column(name="username", type="string", length=50, nullable=true)
     */
    protected $username;

    /**
     * @var string $pass
     * @Column(name="pass", type="string", length=50, nullable=true)
     */
    protected $pass;

    /**
     * @var string $os
     * @Column(name="os", type="string", length=20, nullable=false)
     */
    protected $os;

    /**
     * @var string $osVersion
     * @Column(name="osVersion", type="string", length=20, nullable=true)
     */
    protected $osVersion;

    /**
     * @var string $sshFingerprint
     * @Column(name="sshFingerprint", type="string", length=60, nullable=true)
     */
    protected $sshFingerprint;

    /**
     * @var integer $l3parent
     * @Column(name="l3parent", type="integer", length=11, nullable=true)
     */
    protected $l3parent;

    /**
     * @var string $l3parentIf
     * @Column(name="l3parentIf", type="string", length=50, nullable=true)
     */
    protected $l3parentIf;

    /**
     * @var APNetwork
     * @ManyToOne(targetEntity="APNetwork", inversedBy="AP")
     * @JoinColumns({
     *   @JoinColumn(name="APNetwork_ID", referencedColumnName="ID")
     * })
     */
    protected $network;

}
