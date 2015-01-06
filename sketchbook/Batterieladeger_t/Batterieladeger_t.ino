/***************************************************************
 * Batterieladegerät: Das Ladegerät erkennt, wenn Batterie angeschlossen wird.
 * Gibt Signal an Pin 13 aus und beginnt automatisch zu laden (Knopfdruck).
 * die interne LED an Pin 13 dient als Statusanzeige.
 ****************************************************************/
 
 int button      = 13;
 int timeSignal  = 500;
 int input       = 4;
 int value       = 0;
 bool debug      = 1;
 bool status     = 0; //0 = nicht laden; 1 = laden;
 int schwelle    = 240;

 void setup () {
   if( debug ) Serial.begin( 9600 );
   pinMode( input,INPUT );
   pinMode( button, OUTPUT );
 }
 
 void loop () {
     value = analogRead( input );
     Serial.print("InputValue = " ); 
     Serial.println( value );
     delay(timeSignal);
     digitalWrite(button, HIGH);
     if( value >= schwelle && !status ){
         status = 1;
         digitalWrite(button, LOW);
         delay(timeSignal);
         digitalWrite(button, HIGH);
     }
     
     if( value < schwelle ){
       status = 0;  
     }
 }
 
