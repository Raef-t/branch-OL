import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/failure_state_component.dart';
import '/features/auth/presentation/managers/cubits/auth_cubit.dart';
import '/features/auth/presentation/managers/cubits/auth_state.dart';
import '/features/home/presentation/view/widgets/custom_success_auth_state_in_home_view.dart';
import '/features/home/presentation/view/widgets/shimmer_auth_app_bar_home_view.dart';

class CustomActionsAppBarInHomeView extends StatelessWidget {
  const CustomActionsAppBarInHomeView({
    super.key,
    required this.userName,
    required this.userPhoto,
  });
  final String userName, userPhoto;
  @override
  Widget build(BuildContext context) {
    return BlocBuilder<AuthCubit, AuthState>(
      builder: (context, state) {
        if (state is AuthSuccessState) {
          final userModel = state.authModelInCubit.userModel;
          return CustomSuccessAuthStateInHomeView(
            userModel: userModel,
            userName: userName,
            userPhoto: userPhoto,
          );
        } else if (state is AuthFailureState) {
          return FailureStateComponent(
            errorText: state.errorMessageInCubit,
            onPressed: () => context.read<AuthCubit>().loginMethod(
              uniqueId: 'OAD-00001',
              password: 'password123',
            ),
          );
        } else {
          // return const CircularProgressIndicator();
          return const ShimmerAuthAppBarHomeView();
        }
      },
    );
  }
}
