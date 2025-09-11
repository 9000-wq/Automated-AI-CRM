import pandas as pd
import numpy as np
from sklearn.linear_model import LinearRegression
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.preprocessing import StandardScaler,PowerTransformer
from sklearn.metrics import mean_squared_error, mean_absolute_error, r2_score
data=pd.read_csv("train.csv")[['pickup_longitude','pickup_latitude','dropoff_longitude','dropoff_latitude','pickup_datetime','passenger_count','trip_duration']]
df=pd.DataFrame(data)
df['trip_day']=pd.to_datetime(df['pickup_datetime']).dt.day_name()
df['trip_hour']=pd.to_datetime(df['pickup_datetime']).dt.hour
df['trip_month']=pd.to_datetime(df['pickup_datetime']).dt.month
df['is_weekend']=np.where(df['trip_day'].isin(['Saturday','Sunday']),1,0)
df['day_of_week']=pd.to_datetime(df['pickup_datetime']).dt.dayofweek
from math import radians, sin, cos, sqrt, atan2

def haversine(lat1, lon1, lat2, lon2):
    R = 6371  # km
    lat1, lon1, lat2, lon2 = map(radians, [lat1, lon1, lat2, lon2])
    dlat = lat2 - lat1
    dlon = lon2 - lon1
    a = sin(dlat/2)**2 + cos(lat1)*cos(lat2)*sin(dlon/2)**2
    c = 2 * atan2(sqrt(a), sqrt(1-a))
    return R * c

df['haversine_distance'] = df.apply(
    lambda row: haversine(row['pickup_latitude'], row['pickup_longitude'],
                          row['dropoff_latitude'], row['dropoff_longitude']),
    axis=1
)

df.drop(['pickup_datetime','trip_day','pickup_latitude','pickup_longitude','dropoff_longitude','dropoff_latitude'],axis=1,inplace=True)
X=df.drop('trip_duration',axis=1)

y = PowerTransformer(method='yeo-johnson').fit_transform(df[['trip_duration']])
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
scaler=StandardScaler()
X_train=scaler.fit_transform(X_train)   
X_test=scaler.transform(X_test)
model=LinearRegression()
model.fit(X_train,y_train)      
pred=model.predict(X_test)
mse = mean_squared_error(y_test, pred)
mae = mean_absolute_error(y_test, pred) 
print("Mean Squared Error:", mse)
print("Mean Absolute Error:", mae)
print(cross_val_score(model,X,y,cv=5,scoring='r2').mean())

#AS you can see in this code if we train data on all feature giving in the dataset with all preprocessing steps we are getting r2 score of 0.0 still cross val score is 0.02 
#Now we have construct new meaningful feature like haversine distance, trip hour, trip month, day of week and is weekend
#this give a cross val score of 0.46 which is good improvement
#beause the feature givem in the data set like long lat et does given linear model to learn anything from it
#but when we construct new feature which is meaningful and have some relation with target variable it helps the model to learn better
#feature engineering is very important step in the data science project 
