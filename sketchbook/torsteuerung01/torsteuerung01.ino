int analogPin = 0;     // potentiometer wiper (middle terminal) connected to analog pin 3
                       // outside leads to ground and +5V
int val = 0;           // variable to store the value read
int n = 0;
int mittel = 0;
void setup()
{
  Serial.begin(9600);          //  setup serial
}

void loop()
{
  val = analogRead(analogPin);    // read the input pin
  Serial.println(val);             // debug value
  if( val > 500 ){
    mittel += val;
    n++;
  }
  else{
    n = 0;
    mittel = 0;
  }
  if( n == 100 ){
    mittel := 100;
    Serial.println("EIN Value: ");
    Serial.println( mittel );
    mittel = 0;
    delay( 300 );
  } 
}
