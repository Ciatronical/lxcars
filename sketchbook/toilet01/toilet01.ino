// ---------------------------------------------------------------------------
// Toiletten-Lüter Steuerung direkte Absaugung aus der Kloschüssel
// ---------------------------------------------------------------------------

#include <NewPing.h>

#define TRIGGER_PIN  11  // Arduino pin tied to trigger pin on the ultrasonic sensor.
#define ECHO_PIN     10  // Arduino pin tied to echo pin on the ultrasonic sensor.
#define MAX_DISTANCE 300 // Maximum distance we want to ping for (in centimeters). Maximum sensor distance is rated at 400-500cm.
#define OUTPUT_PIN 8
#define OUTPUT_PIN_INV 9 //Output-Pin invers
#define LED_PIN 13
#define ON_DISTANCE 60
#define LOOPS 10
#define TIME_AFTER 50000
int i, j = 0;
bool debug = 0;
bool fan = 0;
short distance = 0;
bool array[LOOPS];
unsigned long offMillis = 0; //Time to switch off
unsigned long currentMillis  = 0;

NewPing sonar(TRIGGER_PIN, ECHO_PIN, MAX_DISTANCE); // NewPing setup of pins and maximum distance.

void setup() {
  if( debug ) Serial.begin(9600);
  pinMode( OUTPUT_PIN, OUTPUT);
  pinMode( OUTPUT_PIN_INV, OUTPUT);
  pinMode( LED_PIN, OUTPUT);
  digitalWrite( OUTPUT_PIN_INV, 1 );
}

void loop() {
  currentMillis = millis();
  distance = sonar.ping_cm();
  array[i] = distance <= ON_DISTANCE;
  delay(100);    // Wait 50ms between pings (about 20 pings/sec). 29ms should be the shortest delay between pings.
  if( debug ){
    Serial.print( "Distance: " );
    Serial.print( distance ); // Send ping, get distance in cm and print result (0 = outside set distance range)
    Serial.println( "cm" );
    Serial.print( "i = " );
    Serial.println( i );
  }
  if( i++ == LOOPS ){
    i = 0;
    j = 0;
    fan = array[0];
    while( j <= LOOPS ){
      fan &= array[j];
      j++;
    }
    if( fan ){
      digitalWrite( OUTPUT_PIN, 1 );
      digitalWrite( OUTPUT_PIN_INV, 0 );
      digitalWrite( LED_PIN, 1 );
      offMillis = currentMillis + TIME_AFTER;
    }
    if( offMillis <= currentMillis ){
      digitalWrite( OUTPUT_PIN, 0 );
      digitalWrite( OUTPUT_PIN_INV, 1 );
      digitalWrite( LED_PIN, 0 );
    }
  }
}

/*********************************************************************************************************************************
EIN: Array mit n Elementen erzeugen. (Loop) Prüfen ob all Elemente True sind Wenn ja ein Zeitstarten
AUS Array prüfen ob false wenn Ja dann Zeit nicht verlängern.
*************************************************************************************************************************************/
