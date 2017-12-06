
/************************* toilet air discharge*************************/               /***********Einbau AutoSpar (Kabelbelegung)***********/
                                                                                        /*                                                   */
int debug  = 0;                                        // debugging mode on/off         /*           Trigger       - Orange Kabel            */       
                                                                                        /*           Echo          - Grün Kabel              */
int trigger = 11;                                    // sensor trigger                  /*           Luft          - Gelb Kabel lang         */
int echo = 10;                                      // sensor echo                      /*           Output invers - Gelb Kabel kurz         */
int LED = 13;                                      // on board LED                      /*                                                   */
int luft = 8;                                     // luft abzug                         /*****************************************************/
int output_invers = 9;                           // output invers                     
int abstand_max = 70;                           // größste entfernung 
int abstand_min = 50;                          // kleineste entferung
int k,h,f;                                    // hilfevaribale
int fall;                                    // status option
unsigned long delay_intervall = 60000;      // abschaltverzögerung
unsigned long try_intervall = 1500;        // einschaltverzögerung
unsigned long zeit;                       // start zeit verzögerte abschaltung
unsigned long max_zeit = 0;              // zeit für einschaltverzögerung
long zeit_sens = 0;                     // sensor zeit echo
long entf;                             // entfernung
unsigned long abs_zeit = 0;           // endzeit verzögerte abschaltung
unsigned long err_zeit = 0;          // fehlerzeit
unsigned long err_intervall = 1000; // fehler blink led
int LED_state = LOW;


void setup()
{ 
  Serial.begin(9600);
  pinMode(echo,INPUT); 
  pinMode(LED,OUTPUT);
  pinMode(luft,OUTPUT);
  pinMode(trigger,OUTPUT);
  pinMode(output_invers,OUTPUT);
}

void loop()
{
  digitalWrite(trigger,LOW);
  delay(5);
  digitalWrite(trigger,HIGH);
  delay(10);
  digitalWrite(trigger,LOW);

  zeit_sens = pulseIn(echo,HIGH);
  entf = (zeit_sens/2)/29.15;                                 // umrechnung in cm 29,15 schallgeschwindigkeit

  if(debug == 1)
  {
    switch(fall)                                              //status option für debug  
    {
      case 1: Serial.print("____an____");
              Serial.print("\t");
              break;
      
      case 2: Serial.print ("____groeser_an____");
              Serial.print("\t");
              break;
      
      case 3: Serial.print("____delay_aus____");
              Serial.print("\t");
              fall = 0;
              break;
      
      case 4: Serial.print("____keine_messung____");
              Serial.print("\t");
              fall = 0;
              break;
              
      default : digitalWrite(LED,LOW);
                Serial.print ("____aus____");
                Serial.print("\t");
                break;
    } 
    Serial.println("________Status________");              //status aller parameter 
    Serial.print("Entfernung:    ");
    Serial.println(entf);
    Serial.print("Luft:          ");
    Serial.println(digitalRead(luft));
    Serial.print("LED:           ");
    Serial.println(digitalRead(LED));
    Serial.print("Output_invers: ");
    Serial.println(digitalRead(output_invers));
    Serial.println("");
  }

  delay(1000); 
  
  if(entf <= 0 || entf > 4000)                         // fehler anzeige kein messwert LED blinkt
  {
      fall = 4;
      digitalWrite(luft,LOW);         
      digitalWrite(output_invers,LOW);
      
      if(millis() - err_zeit > err_intervall)          // blink intervall der LED
      {
        err_zeit = millis();
        
        if(LED_state == LOW)
        {  
          LED_state = HIGH;
        }
        else
        {
          LED_state = LOW;
        }
        digitalWrite(LED,LED_state);
      }
  }
  
  if(entf > abstand_max)
  {
     max_zeit = millis();
     h = 1;
  }
  
  if(millis() - max_zeit > try_intervall && h == 1)                 // distance überprüfung ob jmd sitzt dann einschalten
  {                                       
     f = 1;
     k = 1;
     h = 0;
     fall = 1;
     zeit = millis();                                              // startzeit verzörgerte abschaltung
               
     digitalWrite(LED,HIGH);
     digitalWrite(luft,HIGH);
     digitalWrite(output_invers,LOW);
  }
  
  if(entf > abstand_min && entf < abstand_max && f == 1)           // vergrößter abstand nicht abschalten
  {  
     fall = 2;
     zeit = millis();

     digitalWrite(LED,HIGH);
     digitalWrite(luft,HIGH);
     digitalWrite(output_invers,LOW);
  }
 
  abs_zeit = millis();                           // endzeit für verzögerte abschaltung
  
  if(abs_zeit - zeit > delay_intervall)          // start der verzögerten abschalltung
  {
    if(entf >= abstand_max && k == 1)            // abschaltung wenn bedingungen erfüllt 
    {
        k = 0;
        f = 0;
        fall = 3;
        
        digitalWrite(LED,LOW);
        digitalWrite(luft,LOW);
        digitalWrite(output_invers,HIGH);
    }
  }
}
