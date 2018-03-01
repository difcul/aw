import sys
sys.path.insert(0, '/home/aw000/DiShIn/')

import ssm
import semanticbase

ssm.semantic_base('/home/aw000/DiShIn/disease.db')

e1 = ssm.get_id('DOID_2841') # Asthma
e2 = ssm.get_id('DOID_3083') # COPD
e3 = ssm.get_id('DOID_4') # Disease

ssm.intrinsic = True
ssm.mica = True

print ('similarity(asthma,COPD) = ' + str(ssm.ssm_lin (e1,e2)))
print ('similarity(asthma,disease) = ' + str(ssm.ssm_lin (e1,e3)))
