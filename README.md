## Installation

The project is based on vagrant environment. Therefor you need to have vagrant and virtualbox installed.

Virtual machine host ip should be pointed in `vagrant-env.rb` (copy from `vagrant-env.rb.example`); Also, you will need to add a row to your hosts file so that `digital.loc` points to machine ip.

To create vagrant machine you need run the following:

`vagrant up`

Sometimes an error may happen during the command execution. You need to press `Ctrl+C` once and then after 5 seconds. Then you can google the error and thus will be able to fix the error. **Note**: you will need to run `vagrant ssh` to fix the error (it will ssh into the vagrant machine).

After successfully installing vagrant machine you need to ssh into vagrant and install composer packages: `composer install`