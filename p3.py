import pandas as pd
from sklearn.preprocessing import OrdinalEncoder,OneHotEncoder
from sklearn.impute import SimpleImputer
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import make_pipeline
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import StandardScaler as StanderdScaler ,LabelEncoder
from sklearn.linear_model import LinearRegression
from sklearn.metrics import mean_squared_error,mean_absolute_error,r2_score
import numpy as np
data=pd.read_csv("auto-mpg.csv")
df=pd.DataFrame(data)
print(df.head())
df=df.replace('?',np.nan)
df['horsepower'] = pd.to_numeric(df['horsepower'], errors='coerce')
X=df.drop('mpg',axis=1)
y=df['mpg']
x_train,x_test,y_train,y_test=train_test_split(X,y,test_size=0.2,random_state=42)
CarSettings=make_pipeline(SimpleImputer(strategy='most_frequent'),OneHotEncoder(handle_unknown='ignore'))
OtherSettings=make_pipeline(SimpleImputer(strategy='mean'),StanderdScaler())
transformer=ColumnTransformer(transformers=[
    ('tf1',OtherSettings,['cylinders','displacement','horsepower','weight','acceleration','model year']),
    ('tf2',CarSettings,['car name']),
    ('tf3',OneHotEncoder(),['origin'])
],remainder='passthrough')

model=LinearRegression()
pipe=make_pipeline(transformer,model)
pipe.fit(x_train,y_train)
y_pred=pipe.predict(x_test)
mse = mean_squared_error(y_test, y_pred)
mae = mean_absolute_error(y_test, y_pred)

# print("Mean Squared Error:", mse)
# print("Mean Absolute Error:", mae)

test_data = {
    'cylinders': [4, 6, 8, 4],
    'displacement': [140, 250, 360, 98],
    'horsepower': [90, 105, 180, np.nan],  # np.nan to test imputer
    'weight': [2400, 3300, 4200, 2100],
    'acceleration': [15.0, 14.5, 12.0, 16.5],
    'model year': [82, 79, 76, 81],
    'origin': [1, 1, 2, 3],
    'car name': [
        'toyota new model',   # unseen in training
        'ford granada l',     # could be seen/unseen
        'honda civic custom', # unseen in training
        'volkswagen rabbit'   # could be seen/unseen
    ]
}

test_df = pd.DataFrame(test_data)
predictions = pipe.predict(test_df)
print("Predictions for test data:", predictions)