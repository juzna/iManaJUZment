/**
* AccessPoint unique identifier
*/
typedef i32 APID

/**
* IP address
*/
typedef string ipAddress

/**
* Coordinates on map
*/
struct coordinates {
  1: required i64 posX,
  2: required i64 posY,
}
