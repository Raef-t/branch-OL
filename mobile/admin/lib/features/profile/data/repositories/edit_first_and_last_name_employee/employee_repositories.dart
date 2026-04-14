import 'package:dartz/dartz.dart';
import '/core/errors/failure_error.dart';
import '/features/profile/presentation/managers/models/edit_first_and_last_name_employee/employee_model.dart';

abstract class EmployeesRepositories {
  Future<Either<FailureError, EmployeeModel>> updateEmployee({
    required int employeeId,
    required String firstName,
    required String lastName,
  });
}
